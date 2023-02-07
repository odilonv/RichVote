<?php

namespace App\Lib;

use App\Model\HTTP\Session;

class MessageFlash
{

    // Les messages sont enregistrés en session associée à la clé suivante
    private static string $cleFlash = "_messagesFlash";

    // $type parmi "success", "info", "warning" ou "danger"
    public static function ajouter(string $type, string $message): void
    {
        $session = Session::getInstance();
        $values = [];
        if($session->contient(MessageFlash::$cleFlash)) {
            $values = $session->lire(MessageFlash::$cleFlash);
        }
        $values[$type][] = $message;
        $session->enregistrer(MessageFlash::$cleFlash, $values);
    }

    public static function contientMessage(string $type): bool
    {
        $session = Session::getInstance();
        return isset($session->lire(MessageFlash::$cleFlash)[$type]) && sizeof($session->lire(MessageFlash::$cleFlash)[$type]);
    }

    // Attention : la lecture doit détruire le message
    public static function lireMessages(string $type): array
    {
        $session = Session::getInstance();
        $messagesType = [];
        if(self::contientMessage($type)){
            $messages = $session->lire(MessageFlash::$cleFlash);
            $messagesType = $messages[$type];
            $messages[$type] = [];
            $session->enregistrer(MessageFlash::$cleFlash, $messages);
        }
        return $messagesType;
    }

    public static function lireTousMessages() : array
    {
        $session = Session::getInstance();
        $messages = [];
        if($session->contient(MessageFlash::$cleFlash)){
            $messages = $session->lire(MessageFlash::$cleFlash);
            $session->enregistrer(MessageFlash::$cleFlash, []);
        }
        return $messages;
    }

}