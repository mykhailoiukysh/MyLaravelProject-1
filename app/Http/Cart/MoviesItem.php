<?php

namespace App\Http\Cart;

use App\Http\Cart\Exception\ItemException;
use App\Http\Models\Movies;

/**
 * Interface ItemInterface.
 */
class MoviesItem implements ItemInterface
{
    /**
     * @var
     */
    protected $id;

    /**
     * Constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function add(ItemInterface $item)
    {
    }

    /**
     * @return mixed
     */
    public function remove(ItemInterface $item)
    {
    }

    /**
     * @return mixed
     */
    public function clear()
    {
    }

    /**
     * @return mixed
     */
    public function all()
    {
    }

    /**
     * @param $id
     *
     * @throws ItemException
     *
     * @return mixed
     */
    public function getMovie()
    {
        $movie = Movies::find($this->id);

        if ($movie->price !== 0) {
            throw new ItemException('Le produit a un prix à 0');
        }

        return $movie;
    }

    /**
     * @throws ItemException
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->getMovie()->id;
    }
}
