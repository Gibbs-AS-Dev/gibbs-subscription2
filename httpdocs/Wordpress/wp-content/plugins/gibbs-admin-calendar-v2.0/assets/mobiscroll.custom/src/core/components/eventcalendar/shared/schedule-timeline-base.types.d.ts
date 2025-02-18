import { IBaseProps } from '../../../base';
import { MbscCalendarNavService } from '../../../shared/calendar-nav/calendar-nav';
import { MbscCalendarDayData } from '../../../shared/calendar-view/calendar-day';
import { MbscCalendarEvent, MbscCalendarEventData, MbscResource } from '../../../shared/calendar-view/calendar-view.types';
import { DateType, ITimezonePlugin } from '../../../util/datetime';
import { MbscEventConnection, MbscNewEventData, MbscSlot } from '../eventcalendar.types';
export interface ISTOptions extends IBaseProps {
    allDayText?: string;
    amText?: string;
    clickToCreate?: boolean | 'single' | 'double';
    checkSize?: number;
    connections?: MbscEventConnection[];
    colorsMap?: {
        [key: number]: MbscCalendarEvent[];
    };
    currentTimeIndicator?: boolean;
    dayNames?: string[];
    dayNamesMin?: string[];
    dayNamesShort?: string[];
    dataTimezone?: string;
    dateFormat?: string;
    dateFormatLong?: string;
    displayTimezone?: string;
    dragTimeStep?: number;
    dragToCreate?: boolean;
    dragToMove?: boolean;
    dragToResize?: boolean;
    endDay: number;
    endTime?: string;
    eventMap?: {
        [key: string]: MbscCalendarEvent[];
    };
    eventOrder?: (event1: MbscCalendarEvent, event2: MbscCalendarEvent) => number;
    exclusiveEndDates?: boolean;
    extendDefaultEvent?: (args: MbscNewEventData) => MbscCalendarEvent;
    externalDrop?: boolean;
    firstDay?: number;
    getDay?: (d: Date) => number;
    getDate?: (y: number, m: number, d: number, h?: number, i?: number, s?: number, u?: number) => Date;
    getMaxDayOfMonth?: (y: number, m: number) => number;
    getMonth?: (d: Date) => number;
    getWeekNumber?: (d: Date) => number;
    getYear?: (d: Date) => number;
    groupBy?: 'date' | 'resource';
    height?: number;
    invalidateEvent?: 'start-end' | 'strict';
    invalidsMap?: {
        [key: number]: MbscCalendarEvent[];
    };
    eventList?: boolean;
    maxDate: number;
    minDate: number;
    monthNames?: string[];
    monthNamesShort?: string[];
    navigateToEvent?: MbscCalendarEvent;
    navigationService: MbscCalendarNavService;
    nextIcon?: string;
    nextIconRtl?: string;
    downIcon?: string;
    newEventText?: string;
    pmText?: string;
    refDate?: DateType;
    resources?: MbscResource[] | null | undefined;
    rowHeight?: 'variable' | 'equal';
    scroll: number;
    selected: number;
    selectedEventsMap: {
        [key: string]: MbscCalendarEvent;
    };
    showAllDay?: boolean;
    showDays?: boolean;
    showEventTooltip?: boolean;
    weekNumbers?: boolean;
    size?: number;
    resolution?: 'year' | 'month' | 'week' | 'day' | 'hour';
    resolutionVertical?: 'day';
    slots?: MbscSlot[] | null | undefined;
    startDay: number;
    startTime?: string;
    timeCellStep: number;
    timeFormat?: string;
    timeLabelStep: number;
    timezonePlugin?: ITimezonePlugin;
    type: 'week' | 'day' | 'month' | 'year';
    virtualScroll?: boolean;
    weekText?: string;
    width?: number;
    eventDragEnd(args: any): boolean;
    onActiveChange(args: any): void;
    onCellClick(args: any): void;
    onCellDoubleClick(args: any): void;
    onCellRightClick(args: any): void;
    onEventClick(args: any): void;
    onEventDoubleClick(args: any): void;
    onEventRightClick(args: any): void;
    onEventDelete(args: any): void;
    onEventHoverIn(args: any): void;
    onEventHoverOut(args: any): void;
    onEventDragEnd?(args: any, inst: any): void;
    onEventDragStart?(args: any, inst: any): void;
    onResourceCollapse?(args: any, inst: any): void;
    onResourceExpand?(args: any, inst: any): void;
    renderDay?(args: MbscCalendarDayData): any;
    renderDayFooter?(args: MbscCalendarDayData): any;
    renderEventContent?(event: MbscCalendarEventData): any;
    renderEvent?(event: MbscCalendarEventData): any;
    renderResource?(resource: MbscResource): any;
    renderResourceHeader?(): any;
    renderResourceFooter?(): any;
    renderSidebar?(resource: MbscResource): any;
    renderSidebarFooter?(): any;
    renderSidebarHeader?(): any;
}
export interface ISTState {
    batchIndexX?: number;
    batchIndexY?: number;
    cellHeight?: number;
    cellWidth?: number;
    dayNameWidth?: number;
    dayWidth?: number;
    dragData?: IDragData;
    eventHeight?: number;
    footerHeight?: number;
    gridWidth?: number;
    gutterHeight?: number;
    hasScrollX?: boolean;
    hasScrollY?: boolean;
    headerHeight?: number;
    isTouchDrag?: boolean;
    parentRowHeight?: number;
    rowHeight?: number;
    scrollContHeight?: number;
    update?: number;
}
export interface IDailyColors {
    allDay?: MbscCalendarEventData;
    colors: MbscCalendarEventData[];
}
export interface IDailyEvents {
    allDay: MbscCalendarEventData[];
    events: MbscCalendarEventData[];
}
export interface IDailyInvalids {
    allDay?: MbscCalendarEventData;
    invalids: MbscCalendarEventData[];
}
export interface IEventPosData {
    cssClass?: string;
    start?: string;
    startDate: Date;
    end?: string;
    endDate: Date;
    position?: {
        height?: string;
        left?: string;
        right?: string;
        top?: string;
        width?: string;
    };
}
export interface IDragData {
    originDate?: number;
    /**
     * The dates of the event which is dragged.
     * We need to display the event boxes in case of touch drag, when we enter drag mode,
     * and continue to display during drag, but invisible, otherwise, if we loose the
     * element on which the touch started, the touch events will stop firing.
     */
    originDates?: {
        [key: string]: MbscCalendarEventData;
    };
    originResource?: number | string;
    /** The dates of the dragged event. */
    draggedDates?: {
        [key: string]: MbscCalendarEventData;
    };
    /** The dragged event, displayed during drag. */
    draggedEvent?: MbscCalendarEventData;
    resource?: number | string;
    slot?: number | string;
}
export interface IDayData {
    columnTitle?: string;
    date: Date;
    dateIndex: number;
    dateKey: string;
    dateText: string;
    day: number;
    dayDiff: number;
    /** True when it is today or when today is in the week/month/year range - with higher resolutions  */
    isActive?: boolean;
    label: string;
    lastOfMonth?: boolean;
    lastOfWeek?: boolean;
    monthIndex: number;
    monthText: string;
    monthTitle: string;
    timestamp: number;
    weekIndex: number;
    weekText: string;
    weekTitle: string;
}
export interface IVirtualPage {
    top: number;
    startIndex: number;
}
