import { ILabelDragData, MbscCalendarEventData } from '../../shared/calendar-view/calendar-view.types';
import { MbscEventList } from './eventcalendar.types.public';
/** @hidden */
export interface MbscEventcalendarState {
    activeDate?: number;
    eventList?: MbscEventList[];
    height?: number;
    isListScrollable?: boolean;
    isTouchDrag?: boolean;
    popoverDate?: number | undefined;
    popoverList?: MbscCalendarEventData[];
    selectedDate?: number;
    showPopover?: boolean;
    labelDragData?: ILabelDragData;
    width?: number;
}
export * from './eventcalendar.types.public';
