import { MbscPageOptions, PageBase } from './page';
import '../../base.scss';
import './page.scss';
export declare function template(s: MbscPageOptions, inst: PageBase, content: any): any;
export declare class Page extends PageBase {
    protected _template(s: MbscPageOptions): any;
}
