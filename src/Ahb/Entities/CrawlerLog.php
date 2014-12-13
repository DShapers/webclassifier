<?php

namespace Ahb\Entities;

/**
 * @Entity @Table(name="crawlerlog", indexes={@Index(name="source_idx", columns={"source"}), @Index(name="crawlstatus_idx", columns={"crawlStatus"}), @Index(name="crawlstartdate_idx", columns={"crawlStartDate"}), @Index(name="crawlenddate_idx", columns={"crawlEndDate"})})
**/
class Crawlerlog
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;
    /** @Column(type="string") **/
    protected $source;
    /** @Column(type="integer") **/
    protected $documentsCrawled;
    /** @Column(type="date") **/
    protected $crawlStartDate;
    /** @Column(type="date") **/
    protected $crawlEndDate;
    /** @Column(type="integer") **/
    protected $crawlStatus;
}
