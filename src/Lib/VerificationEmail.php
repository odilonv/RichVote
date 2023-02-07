<?php

namespace App\Lib;

use App\Model\DataObject\User;
use App\Model\Repository\UserRepository;

class VerificationEmail
{
    public static function envoiEmailValidation(User $utilisateur): void
    {

        $subject = 'Validation de votre compte RichVote';

        $headers = "From: RichVote \r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $message  = '


            <div align="center" style="display:block;height:100%; width:100%;background-color: whitesmoke; justify-content: center">
            <div style="text-align:center;height:100%; width:500px;background-color: white">
            <header style="background: linear-gradient( rgb(113, 117, 213) 0%, rgba(57,78,222,1) 100%);height:100px;">
            <div style="color: white; font-weight:bolder;font-size:26px;padding: 30px 50px 0 0">Validez votre compte RichVote !</div>
            </header>
            
            <div style="margin-top: 30px"> 
            <img alt="voter" src="https://media.discordapp.net/attachments/1050956357748150302/1050956403642224690/img2.png"  width="100px" height="100px"></div>
            <p style="color: black;width:60%;margin-left:auto;margin-right:auto; font-weight:bold;font-size:18px"> Plus qu\'une étape!</p>
            <p style="color: #181818FF;width:60%;margin-left:auto;margin-right:auto;"> Utilisez le code de vérification ci-dessous pour finaliser votre inscription.</p>
            <div style="margin-left: 150px;margin-right: 150px;margin-bottom: 40px ;background:#ffffff;border:2px solid #e2e2e2;line-height:1.1;text-align:center;text-decoration:none;display:block;border-radius:8px;font-weight:bold;padding:10px 40px">
            <span style="color:#333;letter-spacing:5px">' . $utilisateur->getNonce().'</span>
            </div>
            <div style="color:#7C7C7CFF;margin-top: 40px;margin-bottom: 20x;padding:10px 20px 10px 10px">Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet e-mail.</div>
            <footer style="background-color: black"><table role="presentation" width="100%" >
            <tbody><tr>
            <td style="padding:10px 10px 10px 20px" align="left"><div style="color: white;font-weight: bold"> richvote.website@gmail.&#8203;com</div></td> 
            <td style="padding:10px 20px 10px 10px" align="right"><img alt="RichVote" src="https://media.discordapp.net/attachments/1050956357748150302/1052001841463951460/logo.png?width=629&height=629" width="60px" height="60px">  </td> 
            </tr></tbody></table></footer>
            </div></div>
            <span style="opacity: 0">' . $utilisateur->getEmail().'</span>
            ';

        mail($utilisateur->getEmail(),
            $subject,
            $message,
            $headers);
    }


    public static function envoiEmailRecuperation(string $email): void
    {

        $userRepository = new UserRepository();
        $subject = 'Récuperation de votre mot de passe RichVote';

        $headers = "From: RichVote \r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $message  = '

<div align="center" style="display:block;height:100%; width:100%;background-color: whitesmoke; justify-content: center">
<div style="text-align:center;height:100%; width:500px;background-color: white">
<header style="background: linear-gradient( rgb(113, 117, 213) 0%, rgba(57,78,222,1) 100%);height:100px;">
<div style="color: white; font-weight:bolder;font-size:18px;padding: 40px 50px 0 0">Récuperation de votre mot de passe RichVote.</div>
</header>

<div style="margin-top: 30px"> 
<a href="http://localhost/sae-website/web/frontController.php?controller=user&action=update&id='.$userRepository->selectEmail($email)->getMdpHache().'" style="color: cornflowerblue;width:60%;margin-left:auto;margin-right:auto;"> Cliquez sur ce lien pour réinitialiser votre mot de passe RichVote.</a>
<div style="color:#7C7C7CFF;margin-top: 40px;margin-bottom: 20px;padding:10px 20px 10px 10px">Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet e-mail.</div>
<footer style="background-color: black"><table role="presentation" width="100%" >
<tbody><tr>
<td style="padding:10px 10px 10px 20px" align="left"><div style="color: white;font-weight: bold"> richvote.website@gmail.&#8203;com</div></td> 
<td style="padding:10px 20px 10px 10px" align="right"><img alt="RichVote" src="https://media.discordapp.net/attachments/1050956357748150302/1052001841463951460/logo.png?width=629&height=629" width="60px" height="60px">  </td> 
</tr></tbody></table></footer>
</div></div>
<span style="opacity: 0"> ticker#' .rand(0, 1000).'</span>
';

        mail($email,
            $subject,
            $message,
            $headers);
    }
}
