<?php
namespace App\Security\voter;

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConversationVoter extends Voter{
    const VIEW ='view';
    /**
     * @var ConversationRepository
     */
    private $conversationRepository;
    /**
     * @var UtilisateurRepository
     */
    private $utilisateurRepository;

    public function __construct(ConversationRepository $conversationRepository,
                                UtilisateurRepository $utilisateurRepository)
    {
        $this->conversationRepository = $conversationRepository;
        $this->utilisateurRepository = $utilisateurRepository;
    }


    protected function supports(string $attribute, $subject)
    {
        return $attribute == self::VIEW && $subject instanceof Conversation;

    }

    protected function voteOnAttribute(string $attribute,$subject, TokenInterface $token )
    {
        if ($subject->isParticipant($this->utilisateurRepository->findOneBy(array('email' => $token->getUser()->getUsername())))){
            return $subject;
        }
        return null;

    }
}