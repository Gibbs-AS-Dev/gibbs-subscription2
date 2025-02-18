/** @jsxRuntime classic */
/** @jsx createElement */
import { DatepickerBase } from '../../../preact/../core/components/datepicker/datepicker';
import { MbscDatepickerOptions } from '../../../preact/../core/components/datepicker/datepicker.types.public';
import './datepicker.scss';
export { CalendarNext, CalendarPrev, CalendarToday, CalendarNav } from '../../../preact/shared/calendar-header';
export declare function template(s: MbscDatepickerOptions, inst: DatepickerBase, content: any, slots?: any): any;
export declare class Datepicker extends DatepickerBase {
    protected _template(s: MbscDatepickerOptions): any;
}
