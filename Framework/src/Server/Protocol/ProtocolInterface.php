<?php

namespace Server\Protocol;

use Server\Connection\ConnectionInterface;

/**
 * Protocol interface
 */
interface ProtocolInterface
{
	/**
	 * Check the integrity of the package.
	 * Please return the length of package.
	 * If length is unknow please return 0 that mean wating more data.
	 * If the package has something wrong please return false the connection will be closed.
	 *
	 * @param ConnectionInterface $connection
	 * @param string              $recv_buffer
	 * @return int|false
	 */
	public static function input($recv_buffer, ConnectionInterface $connection);

	/**
	 * Decode package and emit onMessage($message) callback, $message is the result that decode returned.
	 *
	 * @param ConnectionInterface $connection
	 * @param string              $recv_buffer
	 * @return mixed
	 */
	public static function decode($recv_buffer, ConnectionInterface $connection);

	/**
	 * Encode package brefore sending to client.
	 *
	 * @param ConnectionInterface $connection
	 * @param mixed               $data
	 * @return string
	 */
	public static function encode($data, ConnectionInterface $connection);
}

// end of script
