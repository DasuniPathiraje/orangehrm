<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Admin\Tests\Dao;

use OrangeHRM\Admin\Dao\JobTitleDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\JobSpecificationAttachment;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 */
class JobTitleDaoTest extends TestCase
{

    private $jobTitleDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->jobTitleDao = new JobTitleDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/JobTitleDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetJobTitleList(): void
    {
        $result = $this->jobTitleDao->getJobTitleList();
        $this->assertEquals(count($result), 3);
    }

    public function testGetJobTitleListWithInactiveJobTitles(): void
    {
        $result = $this->jobTitleDao->getJobTitleList("", "", false);
        $this->assertEquals(count($result), 4);
    }

    public function testDeleteJobTitle(): void
    {
        $toBedeletedIds = array(3, 2);
        $result = $this->jobTitleDao->deleteJobTitle($toBedeletedIds);
        $this->assertEquals($result, 2);
    }

    public function testGetJobTitleById(): void
    {
        $result = $this->jobTitleDao->getJobTitleById(1);
        $this->assertTrue($result instanceof JobTitle);
        $this->assertEquals('Software Architect', $result->getJobTitleName());
    }

    public function testGetJobSpecAttachmentById(): void
    {
        $result = $this->jobTitleDao->getJobSpecAttachmentById(1);
        $this->assertTrue($result instanceof JobSpecificationAttachment);
        $this->assertEquals('Software architect spec', $result->getFileName());
    }
}