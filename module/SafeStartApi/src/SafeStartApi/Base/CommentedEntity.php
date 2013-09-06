<?php

namespace SafeStartApi\Base;

abstract class CommentedEntity extends Entity
{
    /**
     * @var string
     */
    protected $comment_entity = 'system';

    /**
     * @return array
     */
    public function getComments()
    {
        $rows = array();
        $em = \SafeStartApi\Application::getEntityManager();
        $items = $em->getRepository('SafeStartApi\Entity\Comment')->findBy(
            array(
                'entity_id' => $this->getId(),
                'entity' => $this->comment_entity
            )
        );
        foreach ($items as $item) $rows[] = $item->toArray();
        return $rows;
    }

    /**
     * @param $content
     * @return \SafeStartApi\Entity\Comment
     */
    public function addComment($content)
    {
        $em = \SafeStartApi\Application::getEntityManager();
        $comment = new Comment();
        $comment->setEntity($this->comment_entity);
        $comment->setEntityId($this->getId());
        $comment->setContent($content);
        $comment->setUser(\SafeStartApi\Application::getCurrentUser());
        $comment->setAddDate(new \DateTime("now"));
        $comment->setUpdated();
        $em->persist($comment);
        $em->flush();
        return $comment;
    }

}
