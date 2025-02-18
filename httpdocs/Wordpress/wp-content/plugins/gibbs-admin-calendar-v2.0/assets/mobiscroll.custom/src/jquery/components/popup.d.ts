import { Popup as PopupComp } from '../../core/components/popup/popup.common';
export * from '../../core/components/popup/popup.types.public';
declare class Popup extends PopupComp {
    static _fname: string;
    static _renderOpt: import("../../preact/renderer").IRenderOptions;
}
export { Popup };
