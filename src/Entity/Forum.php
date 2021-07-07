<?php

namespace App\Entity;

use App\Repository\ForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ForumRepository::class)
 */
class Forum
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity=Game::class, mappedBy="forum", cascade={"persist", "remove"})
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity=Topic::class, mappedBy="forum")
     */
    private $topic;

    public function __construct()
    {
        $this->topic = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        // set the owning side of the relation if necessary
        if ($game->getForum() !== $this) {
            $game->setForum($this);
        }

        $this->game = $game;

        return $this;
    }

    /**
     * @return Collection|Topic[]
     */
    public function getTopic(): Collection
    {
        return $this->topic;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topic->contains($topic)) {
            $this->topic[] = $topic;
            $topic->setForum($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        if ($this->topic->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getForum() === $this) {
                $topic->setForum(null);
            }
        }

        return $this;
    }
}
