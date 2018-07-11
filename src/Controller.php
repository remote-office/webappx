<?php

	namespace WebAppX;

	/**
	 * Class App
	 *
	 * @author David Betgen <d.betgen@remote-office.nl>
	 * @version 1.0
	 */
	abstract class Controller
	{
		protected $container;

		public function __construct(ContainerInterface $container)
		{
			$this->container = $container;
		}

	  /**
		 * Magic method to access contaniner indirectley
		 *
		 * @param string $property
		 * @return mixed
		 */
		public function __get($property)
		{
			if($this->container->{$property})
				return $this->container->{$property};
		}
	}

?>