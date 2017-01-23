<?php

namespace PLL\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class PLLUserBundle extends Bundle
{
	public function getParent()
	{
		return "FOSUserBundle";
	}
}
