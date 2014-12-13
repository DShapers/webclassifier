<?php

namespace Ahb\Entities;

/**
 * @Entity @Table(name="document", indexes={@Index(name="crawlerid_idx",columns={"crawlerId"}), @Index(name="crawleddate_idx", columns={"crawledDate"})})
**/
class Document
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
    /** @Column(type="integer") **/
    protected $crawlerId;
    /** @Column(type="string") **/
    protected $source;
    /** @Column(type="string") **/
    protected $url;
    /** @Column(type="text") **/
    protected $title;
    /** @Column(type="text") **/
    protected $content;
     /** @Column(type="integer") **/
    protected $size;
    /** @Column(type="date") **/
    protected $crawledDate;
}
