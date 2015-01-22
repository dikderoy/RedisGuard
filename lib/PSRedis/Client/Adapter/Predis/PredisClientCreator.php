<?php


namespace PSRedis\Client\Adapter\Predis;


use RedisGuard\Client;
use RedisGuard\Exception\ConfigurationError;

/**
 * Class PredisClientCreator
 *
 * Factory to create the Predis clients that allow us to talk to Redis and Sentinel nodes.
 *
 * @package RedisGuard\Client\Adapter\Predis
 */
class PredisClientCreator
    implements PredisClientFactory
{
    public function createClient($clientType, array $parameters = array())
    {
        switch($clientType)
        {
            case Client::TYPE_REDIS:
                return $this->createRedisClient($parameters);
            case Client::TYPE_SENTINEL:
                return $this->createSentinelClient($parameters);
        }

        throw new ConfigurationError('To create a client, you need to provide a valid client type');
    }

    private function createSentinelClient(array $parameters = array())
    {
        $predisClient = new \Predis\Client($parameters);
        $predisClient->getProfile()->defineCommand(
            'sentinel', '\\RedisGuard\\Client\\Adapter\\Predis\\Command\\SentinelCommand'
        );
        $predisClient->getProfile()->defineCommand(
            'role', '\\RedisGuard\\Client\\Adapter\\Predis\\Command\\RoleCommand'
        );

        return $predisClient;
    }

    private function createRedisClient(array $parameters = array())
    {
        $predisClient = new \Predis\Client($parameters);
        $predisClient->getProfile()->defineCommand(
            'role', '\\RedisGuard\\Client\\Adapter\\Predis\\Command\\RoleCommand'
        );

        return $predisClient;
    }
}