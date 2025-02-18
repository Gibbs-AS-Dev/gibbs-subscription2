import { PureComponent } from './pure';
export declare function Directive(args: any): (ctor: any) => any;
export declare class Base<PropsType, StateType> extends PureComponent<PropsType, StateType> {
    /** @hidden */
    static _fname: string;
    /** @hidden */
    static _selector: string;
    /** @hidden */
    static _renderOpt: any;
    /** @hidden */
    s: PropsType;
    value: any;
    _el: HTMLElement;
    protected _opt: any;
    protected _newProps: any;
    protected _shouldEnhance?: HTMLElement | string | boolean | null;
    private __value;
    componentDidMount(): void;
    componentDidUpdate(): void;
    componentWillUnmount(): void;
    render(): any;
    getInst(): this;
    setOptions(opt: PropsType): void;
    _setEl: (el: any) => void;
    _safeHtml(html: string): any;
    protected _init(): void;
    protected __init(): void;
    protected _emit(name: string, args: any): void;
    protected _template(s: PropsType, state: StateType): any;
    protected _mounted(): void;
    protected _updated(): void;
    protected _destroy(): void;
    protected __destroy(): void;
    protected _willUpdate(): void;
    private _enhance;
}
