<?php

namespace Ahb\Entities;

/**
 * @Entity @Table(name="entity", indexes={@Index(name="entity_name_idx",columns={"name"})})
**/
class Entity
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    public $id;
    /** @Column(type="string") **/
    public $name;
    /** @Column(type="text") **/
    public $keywords;
     /** @Column(type="integer") **/
    public $documentNumber;
    /** @Column(type="bigint") **/
    public $updateDate;
}
