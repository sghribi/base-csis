<?php

namespace CSIS\EamBundle\Security\Authorization\Voter;

use CSIS\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use CSIS\EamBundle\Entity\Equipment;

/**
 * Class EquipmentVoter
 */
class EquipmentVoter implements VoterInterface
{
    const VIEW = 'view';
    const VIEW_OWNERS = 'view_owners';
    const EDIT = 'edit';
    const EDIT_OWNERS = 'edit_owners';

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::EDIT_OWNERS,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        $supportedClass = 'CSIS\EamBundle\Entity\Equipment';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * {@inheritdoc}
     *
     * @var Equipment $equipment
     */
    public function vote(TokenInterface $token, $equipment, array $attributes)
    {
        if (!$this->supportsClass(get_class($equipment))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW, EDIT or EDIT_OWNERS'
            );
        }

        $attribute = $attributes[0];
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // Utilisateur déconnecté : vue
        if (!$user instanceof User) {
            if ($attribute == self::VIEW) {
                return VoterInterface::ACCESS_GRANTED;
            }

            return VoterInterface::ACCESS_DENIED;
        }

        // Utilisateur connecté : vue totale
        if ($attribute == self::VIEW || $attribute == self::VIEW_OWNERS) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // Test1
        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_GEST_ESTAB')) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // Test 2
        if ($user->hasRole('ROLE_GEST_LAB') && $user->getInstitution() == $equipment->getLaboratory()->getInstitution()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // Test 3
        if ($user->hasRole('ROLE_GEST_EQUIP') && $user->getLab() == $equipment->getLaboratory()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        // Test 4
        switch($attribute) {
            case self::EDIT:
                if ($equipment->getOwners()->contains($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                return VoterInterface::ACCESS_DENIED;
                break;

            case self::EDIT_OWNERS:
                return VoterInterface::ACCESS_DENIED;
                break;
        }
    }
}