import { MbscPageOptions } from '../../core/components/page/page';
import { Page as PageComp } from '../../core/components/page/page.common';
declare class Page extends PageComp {
    static _fname: string;
    static _selector: string;
    static _renderOpt: import("../../preact/renderer").IRenderOptions;
}
export { MbscPageOptions, Page };
