<?php

	namespace WebAppX;
	
  interface ContainerInterface
  {
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id
     * @return mixed
     */
    public function get($id);

    /**
     * Returns true if the container can return an entry for the given identifier.
     *
     * @param string $id
     * @return boolean
     */
    public function has($id);
  }

?>