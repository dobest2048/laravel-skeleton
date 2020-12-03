<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace App\View\Components\Forms;

use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * 地址组件
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Address extends Component
{
    /**
     * The input id attribute.
     *
     * @var string
     */
    public $id;
    /**
     * The input name attribute.
     *
     * @var string
     */
    public $name;
    /**
     * The input label element.
     *
     * @var string
     */
    public $label;

    /**
     * Create a new component instance.
     *
     * @param string $name
     * @param string $label
     *
     * @return void
     */
    public function __construct($name = "address", $label = "Address Input")
    {
        $this->id = 'form-' . Str::kebab(class_basename(get_class($this))) . '-' . md5($name);
        $this->name = $name;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.forms.address');
    }
}
