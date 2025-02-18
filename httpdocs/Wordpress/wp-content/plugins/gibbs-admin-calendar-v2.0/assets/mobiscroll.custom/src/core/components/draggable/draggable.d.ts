import { BaseComponent } from '../../base';
import { MbscDraggableOptions } from './draggable.types.public';
export interface MbscDraggableState {
    hasHover?: boolean;
    hasFocus?: boolean;
}
export declare function subscribeExternalDrag(handler: (value: any) => void): number;
export declare function unsubscribeExternalDrag(key: number): void;
/** @hidden */
export declare class DraggableBase extends BaseComponent<MbscDraggableOptions, MbscDraggableState> {
    private _dragData?;
    private _unlisten?;
    protected _render(s: MbscDraggableOptions): void;
    protected _updated(): void;
    protected _destroy(): void;
}
