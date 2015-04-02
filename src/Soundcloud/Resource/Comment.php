<?php

namespace Njasm\Soundcloud\Resource;

class Comment extends AbstractResource
{
    protected $resource = 'comment';
    protected $writableProperties = ['body', 'track_id', 'user_id', 'timestamp'];

    /**
     * Tries to refresh the resource requesting it to Soundcloud based on it's id.
     *
     * @param bool $returnNew
     * @return AbstractResource|void
     * @throws \Exception
     */
    public function refresh($returnNew = false)
    {
        if (! $this->isNew()) {
            throw new \LogicException("Resource can't be saved because it's not new.");
        }

        $sc = Soundcloud::instance();
        $userID = $sc->getMe()->get('id');
        $id = $this->get('id');
        $url = '/users/' . $userID . '/comments/' . $id;
        $response = $sc->get($url)->send();

        if ($returnNew) {
            return AbstractFactory::unserialize($response->bodyRaw());
        }

        $this->unserialize($response->bodyRaw());
    }

    /**
     * Tries to save the resource in Soundcloud when the resource object is new.
     *
     * @return AbstractResource|void
     * @throws \Exception
     */
    public function save()
    {
        if (! $this->isNew()) {
            throw new \LogicException("Resource can't be saved because it's not new.");
        }

        $sc = Soundcloud::instance();
        $userID = $sc->getMe()->get('id');
        $url = '/users/' . $userID . '/comments';
        $response = $sc->post($url, $this->serialize())->send();

        if ($response->getHttpCode() == 200) {
            return $this->unserialize($response->bodyRaw());
        }

        return AbstractFactory::unserialize($response->bodyRaw());
    }

    public function update()
    {
        throw new \Exception("Can't Update a Comment");
    }

    public function delete()
    {
        throw new \Exception("Can't Delete a Comment");
    }
}