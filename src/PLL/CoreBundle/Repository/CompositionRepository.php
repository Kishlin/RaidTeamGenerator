<?php

namespace PLL\CoreBundle\Repository;

/**
 * CompositionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompositionRepository extends \Doctrine\ORM\EntityRepository
{
	public function getCompositionsFull($guild_id) 
	{
		$query = $this
			->createQueryBuilder("c")
			->where("c.guild = ?1")
			->leftJoin("c.compositionbuilds", "cb")
			->addSelect("cb")
			->leftJoin("cb.build", "b")
			->addSelect("b")
			->setParameter(1, $guild_id)
			->orderBy("c.name", "ASC")
			->addOrderBy("b.name", "ASC")
		;

		return $query
			->getQuery()
			->getResult()
		;
	}

	public function getCompositionsForGuildQuery($guild_id)
	{
		return $this
			->createQueryBuilder("c")
			->where("c.guild = :gid")
			->setParameter('gid', $guild_id)
			->orderBy("c.name", "ASC");
		;
	}
}
