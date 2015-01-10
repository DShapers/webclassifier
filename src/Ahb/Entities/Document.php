<?php

namespace Ahb\Entities;

/**
 * @Entity @Table(name="document", indexes={@Index(name="crawlerid_idx",columns={"crawlerId"}), @Index(name="crawleddate_idx", columns={"crawledDate"})})
**/
class Document
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    public $id;
    /** @Column(type="integer") **/
    public $crawlerId;
    /** @Column(type="string") **/
    public $source;
    /** @Column(type="string") **/
    public $url;
    /** @Column(type="text") **/
    public $title;
    /** @Column(type="text") **/
    public $content;
     /** @Column(type="integer") **/
    public $size;
    /** @Column(type="date") **/
    public $crawledDate;
}
