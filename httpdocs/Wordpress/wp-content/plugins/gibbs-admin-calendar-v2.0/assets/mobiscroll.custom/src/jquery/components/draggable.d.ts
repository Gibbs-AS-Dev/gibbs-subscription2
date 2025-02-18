import { Draggable as DraggableComponent } from '../../core/components/draggable/draggable.common';
import { MbscDraggableOptions } from '../../core/components/draggable/draggable.types.public';
declare class Draggable extends DraggableComponent {
    static _fname: string;
    static _selector: string;
    static _renderOpt: import("../../preact/renderer").IRenderOptions;
}
export { Draggable, MbscDraggableOptions };
