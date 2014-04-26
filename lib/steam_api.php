<?php
//====================================================================================
// PHP STEAM API CLASS
//====================================================================================
// Author:	|LBTG|Regime (regime@livebythegun.com)
// Version:	0.1
// Date:	07/06/2009
//====================================================================================
// Notes:	This is a class that allows you to easily query the Steam community 
// API from PHP. It is required that your PHP installation/configuration allows for 
// the use of sockets. If it doesn't you could ask your hosting provider to enable 
// them for you, or alternatively feel free to re-write this class to use something 
// like cURL. 
// An example on how to use this class has been provided with this package. There 
// should not be any need to edit anything in this file.
//====================================================================================
// Thanks:	Many thanks to Valve for providing us with the Steam community API.
// Also thanks to voogru for publishing the algorythm to calculate 64 bit steam ID's.
//====================================================================================
// http://www.steampowered.com/
// http://www.steamcommunity.com/
// http://www.livebythegun.com/
//====================================================================================

Class SteamAPI 
{
	//- Time out in seconds
	public $timeout = 3;

	//- API hostname 
	private $hostname = "steamcommunity.com";
	private $socket, $ip_address, $service_port;

	/*
	-- Function: 	constructor
	-- Purpose: 	Called when class is created. Used to set some default values.
	-- Arguments: 	none
	-- Returns: 	void
	*/
	public function __construct() 
	{
		//- Get the IP address for steamcommunity.com
		$this->ip_address = gethostbyname($this->hostname);
		//- Get the port for the WWW service
		$this->service_port = getservbyname('www', 'tcp');
	}

	/*
	-- Function: 	get_profile_data
	-- Purpose: 	Retrieves profile data from the Steam API for the specified steam ID.
	-- Arguments: 	$steam_id - A steam ID to perform the API call for
	-- Returns: 	An array filled with the profile data, or null if the profile does not exist
	*/
	public function get_profile_data($steam_id)
	{
		//- Calculate the 64 bit steam ID
		$steam_id64 = $this->get_steamid64($steam_id);
		if (!$steam_id64)
		{
			throw New Exception('Invalid steam ID provided');
		}

		//- Construct HTTP request
		$request = "GET /profiles/" . $steam_id64 . "?xml=1 HTTP/1.1\r\n";
		$request .= "Host: " . $this->hostname . "\r\n";
		$request .= "Connection: Close\r\n\r\n";

		//- Do HTTP request
		$this->steam_connect();
		$response = $this->steam_request($request);
		$this->steam_disconnect();

		//- Check if a 302 response is given. This happens if the user has a 'Custom URL' set in their profile.
		//- If this is the case, make another request to the custom URL
		if (substr($response,0,12) == "HTTP/1.1 302")
		{
			//- Get the custom URL from the response
			if (preg_match( '@Location: http://steamcommunity.com/id/([a-zA-Z0-9_-]+)/@', $response, $location))
			{
				$custom_url = $location[1];
			}
			else
			{
				throw New Exception('Custom URL redirection, but no location found.');
			}

			//- Construct HTTP request
			$request = "GET /id/" . $custom_url . "?xml=1 HTTP/1.1\r\n";
			$request .= "Host: " . $this->hostname . "\r\n";
			$request .= "Connection: Close\r\n\r\n";

			//- Do HTTP request
			$this->steam_connect();
			$response = $this->steam_request($request);
			$this->steam_disconnect();
		}

		$this->steam_validate($response);

		//- If there is no profile XML element, something is not right
		if (!preg_match( '@<profile>(.+)</profile>@s', $response, $xmlArray))
		{
			throw New Exception('Could not parse returned steam community data. XML Root node missing.');
		}

		//- Store XML without headers
		$xml_data = $xmlArray[0];

		//- Parse XML. An array of data is returned
		$data_array = $this->parse_xml($xml_data);

		//- Return the data
		return $data_array;
	}

	/*
	-- Function: 	parse_xml
	-- Purpose:	Populate an array with the data found in the supplied XML
	-- Arguments:	$xml_data - The XML data to parse
	-- Returns: 	An array of data from the XML
	*/
	private function parse_xml($xml_data)
	{
		//- Create an XMLReader object to parse the XML with
		$reader = new XMLReader();

		//- Load the XML into it
		try {
			$reader->XML($xml_data);
		}
		catch (Exception $e) 
		{
			throw New Exception('Could not parse returned XML: ' . $e->getMessage());
		}

		//- Initialize an array for the profile data
		$data_array = array();

		//- Start reading through the XML
		while ($reader->read())
		{
			//- First encounter the root element
			if ($reader->nodeType == XMLReader::ELEMENT)
			{
				$rootnode_name = $reader->name;

				//- Read through its subnodes
				while ($reader->read())
				{
					if ($reader->nodeType == XMLReader::ELEMENT)
					{
						//- Store the name of the subnode
						$elementName = $reader->name;

						//- Check for sections that will result in a recursive multi dimensional array
						if (	$elementName == "friends" || $elementName == "weblinks" || 
							$elementName == "groups" || $elementName == "mostPlayedGames" )
						{
							//- Initialize an array for the data
							$sub_array = null;
							$sub_array = array();

							$sub_counter = 0;

							while ($reader->read())
							{
								//- Check for subnodes
								if ($reader->nodeType == XMLReader::ELEMENT && 
									($reader->name == "friend" || 
									$reader->name == "weblink" ||
									$reader->name == "group" ||
									$reader->name == "mostPlayedGame"))
								{
									while ($reader->read())
									{
										//- Get the elements from the subnodes
										if ($reader->nodeType == XMLReader::ELEMENT)
										{
											$subElementName = $reader->name;

											//- The node is not empty so an end node is required
											//- Consider the data invalid until it is found
											$validDatafield = false;

											while ($reader->read())
											{
												//- Check for the subnode value
												if ($reader->nodeType == XMLReader::TEXT
													|| $reader->nodeType == XMLReader::CDATA
													|| $reader->nodeType == XMLReader::WHITESPACE
													|| $reader->nodeType == 
														XMLReader::SIGNIFICANT_WHITESPACE)
												{
													//- Store the value
													$elementValue = $reader->value;
												}
												//- Check if this is the subnode end element
												else if ($reader->nodeType == XMLReader::END_ELEMENT
													&& $reader->name == $subElementName)
												{
													//- If so the subnode is complete
													$validDatafield = true;
													break;
												}
											}

											//- If this is a valid subnode, store it in the data array
											if ($validDatafield)
											{
												$sub_array[$sub_counter][$subElementName] = 
													$elementValue;

												$elementValue = '';
											}
										}								
										else if ($reader->nodeType == XMLReader::END_ELEMENT && 
												($reader->name == "friend" ||
												$reader->name == "weblink" ||
												$reader->name == "group" ||
												$reader->name == "mostPlayedGame" ))
										{
											$sub_counter++;
											break;
										}
									}
								}
								else if ($reader->nodeType == XMLReader::END_ELEMENT
									&& $reader->name == $elementName)
								{
									$validDatafield = false;
									$data_array[$elementName] = $sub_array;
									break;
								}
							}
						}
						//- Check for sections that will result in a recursive single dimensional array
						else if ( $elementName == "inGameInfo" || $elementName == "favoriteGame" )
						{
							//- Initialize an array for the data
							$sub_array = null;
							$sub_array = array();

							while ($reader->read())
							{
								//- Get the elements from the subnodes
								if ($reader->nodeType == XMLReader::ELEMENT)
								{
									$subElementName = $reader->name;

									//- The node is not empty so an end node is required
									//- Consider the data invalid until it is found
									$validDatafield = false;

									while ($reader->read())
									{
										//- Check for the subnode value
										if ($reader->nodeType == XMLReader::TEXT
										    || $reader->nodeType == XMLReader::CDATA
										    || $reader->nodeType == XMLReader::WHITESPACE
										    || $reader->nodeType ==
	  										  XMLReader::SIGNIFICANT_WHITESPACE)
										{
											//- Store the value
											$elementValue = $reader->value;
										}
										//- Check if this is the subnode end element
										else if ($reader->nodeType == XMLReader::END_ELEMENT
											&& $reader->name == $subElementName)
										{
											//- If so the subnode is complete
											$validDatafield = true;
											break;
										}
									}

									//- If this is a valid subnode, store it in the data array
									if ($validDatafield)
									{
					 					$sub_array[$subElementName] = $elementValue;
										$elementValue = '';
									}
								 }
								 else if ($reader->nodeType == XMLReader::END_ELEMENT &&
								 (  $reader->name == "inGameInfo" ||
									$reader->name == "favoriteGame" ))
								 {
									$validDatafield = false;
									$data_array[$elementName] = $sub_array;
									break;
								 }
							 }
						}
						//- Check for single nodes that do not require a recursive array to be used
						else
						{
							//- An end node is required
							//- Consider the data invalid until it is found
							$validDatafield = false;

							while ($reader->read())
							{
								//- Check for the subnode value
								if ($reader->nodeType == XMLReader::TEXT
									|| $reader->nodeType == XMLReader::CDATA
									|| $reader->nodeType == XMLReader::WHITESPACE
									|| $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE)
								{
									//- Store the value
									$elementValue = $reader->value;
								}
								//- Check if this is the subnode end element
								else if ($reader->nodeType == XMLReader::END_ELEMENT
									&& $reader->name == $elementName)
								{
									//- If so the subnode is complete
									$validDatafield = true;
									break;
								}
							}

							//- If this is a valid subnode, store it in the data array
							if ($validDatafield)
							{
								$data_array[$elementName] = $elementValue;
								$elementValue = '';
							}
						}
					}
					//- Check for the final rootnode end element
					else if ($reader->nodeType == XMLReader::END_ELEMENT && $reader->name == $rootnode_name)
					{
						break;
					}
				}
			}
		}

		//- Close the XMLReader
		$reader->close();
		
		//- Add the regular steam ID to the array as well
		$data_array['steamID32'] = $this->calculate_steamid($data_array['steamID64']);

		return $data_array;
	}

	/*
	-- Function:	get_steamid64
	-- Purpose:	Check whether we have a regular steam ID, or a 64 bit steam ID and return a 64 bit steam ID either way
	-- Arguments:	$steam_arg - Either a regular, or a 64 bit steam ID
	-- Returns:	The 64 bit steam ID, or false if an invalid argument is provided
	*/
	private function get_steamid64($steam_arg)
	{
		if (preg_match('/^STEAM_[0-9]:[0-9]:[0-9]{1,}/i', $steam_arg))
		{
			return $this->calculate_steamid64($steam_arg);
		}
		else if (preg_match('/^76561197960[0-9]{6}$/i', $steam_arg))
		{
			return $steam_arg;
		}
		else
		{
			return false;
		}
	}

	/*
	-- Function:	calculate_steamid64
	-- Purpose:	Translate a steam ID to a 64 bit steam id as used by Valve
	-- Arguments:	$steam_id - The steam ID to translate
	-- Returns:	The 64 bit steam ID, or false if an invalid steam ID is provided
	*/
	public function calculate_steamid64($steam_id)
	{
		if (preg_match('/^STEAM_[0-9]:[0-9]:[0-9]{1,}/i', $steam_id))
		{
			$steam_id = str_replace("_", ":", $steam_id);
			list($part_one, $part_two, $part_three, $part_four) = explode(':', $steam_id);
			$result = bcadd('76561197960265728', $part_four * 2);
			$result = bcadd($result, $part_two);
			return bcadd($result, $part_three);
		}
		else
		{
			return false;
		}
	}

	/*
	-- Function:	calculate_steamid
	-- Purpose:	Translate a 64 bit steam ID to a steam id as used by Valve
	-- Arguments:	$steam_id64 - The 64 bit steam ID to translate
	-- Returns:	The steam ID, or false if an invalid 64 bit steam ID is provided
	*/
	public function calculate_steamid($steam_id64)
	{
		if (preg_match('/^76561197960[0-9]{6}$/i', $steam_id64))
		{
			$part_one = substr( $steam_id64, -1) % 2 == 0 ? 0 : 1;
			$part_two = bcsub( $steam_id64, '76561197960265728' );
			$part_two = bcsub( $part_two, $part_one );
			$part_two = bcdiv( $part_two, 2 );
			return "STEAM_0:" . $part_one . ':' . $part_two;
		}
		else
		{
			return false;
		}
	}
	
	/*
	-- Function: 	steam_connect
	-- Purpose: 	Initialize a connection to the Steam API
	-- Arguments: 	none
	-- Returns: 	void
	*/
	private function steam_connect()
	{
		//- Get the TCP protocol (can also use SOL_TCP instead)
		$protocol = getprotobyname('tcp');

		//- Create a socket
		$this->socket = socket_create(AF_INET, SOCK_STREAM, $protocol);
		if ($this->socket === false)
			throw New Exception('Could not create socket. Reason: ' . socket_strerror(socket_last_error()));

		//- The socket should time out after 3 seconds, so make it non blocking for now
		socket_set_nonblock($this->socket);

		$error = NULL;
		$attempts = 0;
		//- The time out value has to be in milliseconds
		$timeout_ms = $this->timeout * 1000;
		$connected;

		//- Connect the socket to the steam server
		while (!($connected = @socket_connect($this->socket, $this->ip_address, $this->service_port)) && $attempts++ < $timeout_ms)
		{
			$error = socket_last_error();

			//- If the error is different from the below, there is a problem
			if ($error != SOCKET_EINPROGRESS && $error != SOCKET_EALREADY)
			{
				socket_close($this->socket);
				throw New Exception('Could not connect to steam community: ' . socket_strerror($error));
				exit;
			}

			//- Wait 1 second between attempts
			usleep(1000);
		}

		//- If it's still not connected then consider the connection timed out
		if (!$connected)
		{
			socket_close($this->socket);
			throw New Exception('Connection to steam community timed out.');
			exit;
		}

		//- Make the socket blocking again
		socket_set_block($this->socket);
	}

	/*
	-- Function: 	steam_request
	-- Purpose: 	Sends a HTTP request to the Steam API
	-- Arguments: 	$request_date - The HTTP request to be sent
	-- Returns: 	The 'raw' response data received from the Steam API
	*/
	private function steam_request($request_data)
	{
		//- Write the request to the socket
		socket_write($this->socket, $request_data, strlen($request_data));

		//- Store the response
		$buffer = '';
		$response = '';
		while ($buffer = socket_read($this->socket, 2048))
		{
			$response .= $buffer;
		}

		return $response;
	}

	/*
	-- Function: 	steam_validate
	-- Purpose: 	Checks the HTTP response code to see if the API request was succesful
	-- Arguments: 	$response_data - The response to be validated
	-- Returns: 	void
	*/
	private function steam_validate($response_data)
	{
		//- Check if the HTTP response code is 2xx. If so the request was a success
		if (!preg_match('@^HTTP/1.1 2[0-9]{2}@s', $response_data))
		{
			throw New Exception('Request made to steam community failed (HTTP response code ' .
				substr($this->response, 9, 3) . ')');
		}
	}

	/*
	-- Function: 	steam_disconnect
	-- Purpose: 	Disconnect from the Steam API
	-- Arguments: 	none
	-- Returns: 	void
	*/
	private function steam_disconnect()
	{
		//- Close the socket
		socket_close($this->socket);
	}
}

?>
