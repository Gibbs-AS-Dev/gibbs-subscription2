import { Dropdown as DropdownComp } from '../../core/components/input/dropdown.common';
import { Input as InputComp } from '../../core/components/input/input.common';
import { Textarea as TextareaComp } from '../../core/components/input/textarea.common';
declare class Input extends InputComp {
    static _fname: string;
    static _selector: string;
    static _renderOpt: import("../../preact/renderer").IRenderOptions;
}
declare class Dropdown extends DropdownComp {
    static _fname: string;
    static _selector: string;
    static _renderOpt: import("../../preact/renderer").IRenderOptions;
}
declare class Textarea extends TextareaComp {
    static _fname: string;
    static _selector: string;
    static _renderOpt: import("../../preact/renderer").IRenderOptions;
}
export * from '../../core/components/input/input.types.public';
export { Dropdown, Input, Textarea };
