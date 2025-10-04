<?php

namespace Sillove\Productlabels\Model;

use Magento\Framework\Data\OptionSourceInterface;

class FontsizeList implements OptionSourceInterface
{
    /**
     * Get option array for the dropdown.
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $options['0'] = __('Default Size');
        $options['16'] = __('16px');
        $options['17'] = __('17px');
        $options['18'] = __('18px');
        $options['19'] = __('19px');
        $options['20'] = __('20px');
        $options['21'] = __('21px');
        $options['22'] = __('22px');
        $options['23'] = __('23px');
        $options['24'] = __('24px');
        $options['25'] = __('25px');
        $options['26'] = __('26px');
        $options['27'] = __('27px');
        $options['28'] = __('28px');
        $options['29'] = __('29px');
        $options['30'] = __('30px');
        $options['31'] = __('31px');
        $options['32'] = __('32px');
        $options['33'] = __('33px');
        $options['34'] = __('34px');
        $options['35'] = __('35px');
        $options['36'] = __('36px');
        $options['37'] = __('37px');
        $options['38'] = __('38px');
        $options['39'] = __('39px');
        $options['40'] = __('40px');
        $options['41'] = __('41px');
        $options['42'] = __('42px');
        $options['43'] = __('43px');
        $options['44'] = __('44px');
        $options['45'] = __('45px');
        $options['46'] = __('46px');
        $options['47'] = __('47px');
        $options['48'] = __('48px');
        $options['49'] = __('49px');
        $options['50'] = __('50px');
        $options['51'] = __('51px');
        $options['52'] = __('52px');
        $options['53'] = __('53px');
        $options['54'] = __('54px');
        $options['55'] = __('55px');
        $options['56'] = __('56px');
        $options['57'] = __('57px');
        $options['58'] = __('58px');
        $options['59'] = __('59px');
        $options['60'] = __('60px');
        $options['61'] = __('61px');
        $options['62'] = __('62px');
        $options['63'] = __('63px');
        $options['64'] = __('64px');
        $options['65'] = __('65px');
        $options['66'] = __('66px');
        $options['67'] = __('67px');
        $options['68'] = __('68px');
        $options['69'] = __('69px');
        $options['70'] = __('70px');
        return $options;
    }

    /**
     * Get all options for the dropdown
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);
        return $res;
    }

    /**
     * Get options for the dropdown.
     *
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    /**
     * Convert options array to option array format.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
