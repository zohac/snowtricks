<?php

// /src/AppBundle/Entity/Video.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Video.
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VideoRepository")
 */
class Video
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     * @Assert\Regex(
     *     pattern="#^(http|https)://(www.youtube.com|www.dailymotion.com|vimeo.com)/#",
     *     match=true,
     *     message="L'url doit correspondre à l'url d'une vidéo Youtube, DailyMotion ou Vimeo"
     * )
     */
    private $url;

    /**
     * @var Trick
     *
     * @ORM\ManyToOne(
     *      targetEntity="AppBundle\Entity\Trick",
     *      cascade={"persist"},
     *      inversedBy="videos"
     * )
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid
     */
    private $trick;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return Video
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get iframe.
     *
     * @return string
     */
    public function getIframe()
    {
        // Si c’est une url Youtube on execute la fonction correspondante
        if (preg_match('#^(http|https)://www.youtube.com/#', $this->url)) {
            return $this->getYoutubeIframe();
        }
        // Si c’est une url Dailymotion on execute la fonction correspondante
        if ((preg_match('#^(http|https)://www.dailymotion.com/#', $this->url))) {
            return $this->getDailymotionIframe();
        }
        // Si c’est une url Vimeo on execute la fonction correspondante
        if ((preg_match('#^(http|https)://vimeo.com/#', $this->url))) {
            return $this->getVimeoIframe();
        }
    }

    public function getYoutubeIframe()
    {
        $video = explode('v=', $this->url);

        $iframe = "<iframe width='480' height='360' src='https://www.youtube.com/embed/";
        $iframe .= $video[count($video) - 1];
        $iframe .= "' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen></iframe>";

        return $iframe;
    }

    public function getDailymotionIframe()
    {
        $video = explode('/', $this->url);

        $iframe = "<iframe width='480' height='360' src='//www.dailymotion.com/embed/video/";
        $iframe .= $video[count($video) - 1];
        $iframe .= "' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen></iframe>";

        return $iframe;
    }

    public function getVimeoIframe()
    {
        $video = explode('/', $this->url);

        $iframe = "<iframe width='480' height='360' src='https://player.vimeo.com/video/";
        $iframe .= $video[count($video) - 1];
        $iframe .= "' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";

        return $iframe;
    }

    public function getThumbnail()
    {
        // Si c’est une url Youtube on execute la fonction correspondante
        if (preg_match(
            "#(?:https?:\/{2})?(?:w{3}.)?youtu(?:be)?.(?:com|be)(?:\/watch\?v=|\/)([^s&]+)#",
            $this->url,
            $match
        )
            ) {
            $image = 'https://img.youtube.com/vi/'.$match[1].'/hqdefault.jpg';

            return $image;
        }
        // Si c’est une url Dailymotion on execute la fonction correspondante
        if (preg_match(
            "#(?:https?:\/{2})?(?:w{3}.dailymotion.com\/video\/)([^s&]+)#",
            $this->url,
            $match
        )
            ) {
            $image = 'https://www.dailymotion.com/thumbnail/150x120/video/'.$match[1].'';

            return $image;
        }
        // Si c’est une url Vimeo on execute la fonction correspondante
        if (preg_match(
            "#(?:https?:\/{2})?(?:vimeo.com\/)([^s&]+)#",
            $this->url,
            $match
        )
            ) {
            $hash = unserialize(file_get_contents('https://vimeo.com/api/v2/video/'.$match[1].'.php'));
            $image = $hash[0]['thumbnail_small'];

            return $image;
        }
    }

    /**
     * Set trick.
     *
     * @param \AppBundle\Entity\Trick $trick
     *
     * @return Video
     */
    public function setTrick(\AppBundle\Entity\Trick $trick = null)
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * Get trick.
     *
     * @return \AppBundle\Entity\Trick
     */
    public function getTrick()
    {
        return $this->trick;
    }
}
