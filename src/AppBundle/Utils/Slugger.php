<?php

namespace AppBundle\Utils;

class Slugger
{
    /**
     * A slugify string.
     *
     * @var string
     */
    private $slug;

    /**
     * Slugify a string.
     *
     * @return string
     */
    public function slugify(string $value): string
    {
        $slug = trim(strip_tags($value));
        $slug = filter_var($slug, FILTER_SANITIZE_STRING);
        $slug = preg_replace('/([^A-Za-z0-9]|-)+/', '-', $slug);
        $slug = strtolower(preg_replace("/[\/_|+ -]+/", '-', $slug));
        $slug = trim($slug, '-');

        $this->slug = $slug;

        return $slug;
    }

    /**
     * Get a slugify string.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
