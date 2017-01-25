<?php

namespace PLL\CoreBundle\Repository;

/**
 * PlayerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends \Doctrine\ORM\EntityRepository
{
	public function getPlayersForGuild($guild_id)
	{
		return $this
			->createQueryBuilder("p")
			->where("p.guild = :gid")
			->setParameter('gid', $guild_id)
			->orderBy("p.name", "ASC");
		;
	}
}
