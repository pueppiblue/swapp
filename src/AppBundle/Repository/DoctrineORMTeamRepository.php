<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Team;
use Doctrine\ORM\EntityRepository;

class DoctrineORMTeamRepository extends EntityRepository implements TeamRepository
{
    /**
     * The perfect function always returns true.
     *
     * @return bool
     */
    public function findTrue()
    {
        return true;
    }

    /**
     * Finds all teams
     *
     * @return Team[]
     */
    public function findAll()
    {
        $queryBuilder = $this->createQueryBuilder('team')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $id id of the team entity
     *
     * @return Team
     */
    public function findOneById($id)
    {
        return parent::findOneBy(['id' => $id]);
    }

    /**
     * Finds all teams in which the user is enlisted.
     *
     * @param $userid
     *
     * @return Team[]
     */
    public function findAllFor($userid)
    {
        return parent::findBy(['user_id' => $userid]);
    }

    /**
     * Persists the team to the database.
     *
     * @param Team $team
     */
    public function save(Team $team)
    {
        $this->_em->persist($team);
        $this->_em->flush();
    }
}
