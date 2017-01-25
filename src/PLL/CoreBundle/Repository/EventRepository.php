<?php

namespace PLL\CoreBundle\Repository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends \Doctrine\ORM\EntityRepository
{
	public function getEventsFull($guild_id)
	{
		$query = $this
			->createQueryBuilder("e")
			->leftJoin("e.compositions", "c")
			->addSelect("c")
			->leftJoin("e.players", "p")
			->addSelect("p")
			->where("e.guild = :gid")
			->setParameter("gid", $guild_id)
			->addOrderBy("e.date", "DESC")
		;

		return $query
			->getQuery()
			->getResult()
		;
	}
}
