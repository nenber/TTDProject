<?php

namespace App\Entity;

use App\Repository\ToDoListRepository;
use App\Service\ToDoListService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=ToDoListRepository::class)
 */
class ToDoList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="items")
     */
    private $Items;

    public function __construct()
    {
        $this->Items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function add($item)
    {
        if (!$item instanceof Item) {
            throw new \Exception("Impossible d'ajouter un item, le type est incorrect");
        }

        if (count($this->getItems()) == 8) {
            //envoyer l'email
            if (count($this->getItems()) > 0 && $this->getItems() != null) {
                $currentItem = $this->getItems()[0];
                foreach ($this->getItems() as $key => $value) {
                    if ($value->getDate() > $currentItem->getDate()) {
                        $currentItem = $value;
                    }
                }
                $resultInterval = abs(($currentItem->getdate())->getTimestamp() - (new \DateTime)->getTimestamp()) / 60;
                if ($resultInterval > 30) {
                    throw new \Exception("Un item a déjà été ajouté il y a moins de 30 minutes");
                }
                $this->addItem($item);
            } else {
                $this->addItem($item);
            }
        }
        if (count($this->getItems()) < 10) {
            if (count($this->getItems()) > 0 && $this->getItems() != null) {
                $currentItem = $this->getItems()[0];
                foreach ($this->getItems() as $key => $value) {
                    if ($value->getDate() > $currentItem->getDate()) {
                        $currentItem = $value;
                    }
                }
                $resultInterval = abs(($currentItem->getdate())->getTimestamp() - (new \DateTime)->getTimestamp()) / 60;
                if ($resultInterval > 30) {
                    throw new \Exception("Un item a déjà été ajouté il y a moins de 30 minutes");
                }
                $this->addItem($item);
            } else {
                $this->addItem($item);
            }
            //ajouter l'item
        } else {
            throw new \Exception("Nombre d'items déjà atteint");
        }
        var_dump(count($this->getItems()));
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->Items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->Items->contains($item)) {
            $this->Items[] = $item;
            $item->setItems($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->Items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getItems() === $this) {
                $item->setItems(null);
            }
        }

        return $this;
    }
}
