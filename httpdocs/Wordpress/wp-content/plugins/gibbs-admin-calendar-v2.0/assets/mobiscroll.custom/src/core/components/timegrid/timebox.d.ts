/** @jsxRuntime classic */
/** @jsx createElement */
import { createElement } from '../../../preact/renderer';
import { ITimeSlot } from './timegrid';
export interface ITimeBoxProps {
    disabled: boolean;
    selected: boolean;
    theme: string;
    timeSlot: ITimeSlot;
    onKeyDown(event: any): void;
    onClick(value: ITimeSlot): void;
}
export default function TimeBox({ disabled, selected, theme, timeSlot, onClick, onKeyDown }: ITimeBoxProps): createElement.JSX.Element;
