import { PickerBase } from './picker';
import { IPickerProps, IPickerState } from './picker.types';
export declare function getNativeElement(input: any, callback: (input: HTMLInputElement) => void): void;
export declare function initPickerElement(el: HTMLElement, inst: PickerBase<IPickerProps, IPickerState>, handleChange?: (ev: any) => void, handleClick?: (ev: any) => void): () => void;
