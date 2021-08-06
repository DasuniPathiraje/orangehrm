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

namespace OrangeHRM\Leave\Api;

use DateTime;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Leave\Api\Model\LeavePeriodModel;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;

class LeavePeriodAPI extends Endpoint implements ResourceEndpoint
{
    use LeavePeriodServiceTrait;

    public const PARAMETER_START_MONTH = 'startMonth';
    public const PARAMETER_START_DAY = 'startDay';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $leavePeriodHistory = $this->getLeavePeriodService()->getCurrentLeavePeriodStartDateAndMonth();
        $this->throwRecordNotFoundExceptionIfNotExist($leavePeriodHistory, LeavePeriodHistory::class);
        return new EndpointResourceResult(LeavePeriodModel::class, $leavePeriodHistory);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getIdParamRule()
        );
    }

    /**
     * @return ParamRule
     */
    private function getIdParamRule(): ParamRule
    {
        return new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::POSITIVE));
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setStartMonth(
            $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_START_MONTH)
        );
        $leavePeriodHistory->setStartDay(
            $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_START_DAY)
        );
        $leavePeriodHistory->setCreatedAt(new DateTime());
        $this->getLeavePeriodService()
            ->getLeavePeriodDao()
            ->saveLeavePeriodHistory($leavePeriodHistory);
        return new EndpointResourceResult(LeavePeriodModel::class, $leavePeriodHistory);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_START_MONTH,
                new Rule(Rules::IN, [$this->getLeavePeriodService()->getMonthNumberList()])
            ),
            new ParamRule(
                self::PARAMETER_START_DAY,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::CALLBACK, [
                    function (int $startDay) {
                        $startMonth = $this->getRequestParams()->getInt(
                            RequestParams::PARAM_TYPE_BODY,
                            self::PARAMETER_START_MONTH
                        );
                        $allowedDaysForMonth = $this->getLeavePeriodService()->getListOfDates($startMonth, false);
                        return in_array($startDay, $allowedDaysForMonth);
                    }
                ])
            ),
            $this->getIdParamRule()
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}