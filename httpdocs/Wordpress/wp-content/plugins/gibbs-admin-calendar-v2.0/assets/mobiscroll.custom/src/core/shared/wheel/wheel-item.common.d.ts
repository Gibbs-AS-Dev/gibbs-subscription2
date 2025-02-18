/** @jsxRuntime classic */
/** @jsx createElement */
import { createElement } from '../../../preact/renderer';
import { IWheelItemProps, WheelItemBase } from './wheel-item';
/** @hidden */
export declare function template(s: IWheelItemProps, inst: WheelItemBase): createElement.JSX.Element;
export declare class WheelItem extends WheelItemBase {
    protected _template(s: IWheelItemProps): any;
}
