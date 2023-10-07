<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Repository\ContactRepository;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    private ContactRepository $contact;

    public function __construct(ContactRepository $contact)
    {
        $this->contact = $contact;
    }

    // Pour ajouter une fonction à Twig, on doit définir une méthode getFunctions()
    public function getFunctions(): array
    {
        return [
            new TwigFunction('decodeHtmlEntities', [$this, 'decodeHtmlEntities']),
            new TwigFunction('truncate', [$this, 'truncate']),
            new TwigFunction('countUnreadMessages', [$this, 'countUnreadMessages']),
            new TwigFunction('countMessagesArchived', [$this, 'countMessagesArchived']),
        ];
    }

    //fonction pour décoder les entités html
    public function decodeHtmlEntities(string $string, int $slice = null, int $quoteStyle = ENT_QUOTES, string $charset = 'UTF-8'): string //phpcs:ignore
    {
        $string = html_entity_decode($string, $quoteStyle, $charset);
        if ($slice) {
            $string = substr($string, 0, $slice);
        }
        return $string;
    }

    //fonction pour couper une chaine de caractère
    public function truncate(string $string, int $length = 100, string $append = '...'): ?string
    {
        $string = trim($string);
        if (strlen($string) > $length) {
            $string = wordwrap($string, $length);
            $string = $string ? explode("\n", $string)[0] : $string;
            $string = $string . $append;
        }
        return $string;
    }

    /**
     * Retrieves the number of unread messages.
     * @return bool|float|int|string|null The number of unread messages.
     */
    public function countUnreadMessages()
    {
        return $this->contact->countUnreadMessages();
    }

     /**
     * Retrieves the number of archived messages.
     * @return bool|float|int|string|null The number of archived messages.
     */
    public function countMessagesArchived()
    {
        return $this->contact->countMessagesArchived();
    }
}
