/** @jsxRuntime classic */
/** @jsx createElement */
import { createElement } from '../../../preact/renderer';
import { IScrollviewBaseProps, ScrollviewBase } from './scroll-view';
export declare function template(s: IScrollviewBaseProps, inst: ScrollviewBase, content: any): createElement.JSX.Element;
export declare class Scrollview extends ScrollviewBase {
    protected _template(s: IScrollviewBaseProps): any;
}
