import { createElement, PureComponent } from '../../../preact/renderer';
import { CalendarViewBase } from './calendar-view';
import { ICalendarViewHost } from './calendar-view.types';
export declare const CalendarContext: import("../../../preact/lib/src").Context<{
    children?: any;
    instance?: CalendarViewBase;
}>;
/** @hidden */
export interface IInstanceSubscriberProps {
    /**
     * Host is only set if header controls are placed outside of the calendar,
     * and the host instance (eventcalendar or calendar) is passed to the header control using the `calendar` option.
     */
    host?: ICalendarViewHost;
    component: any;
    /**
     * View is only set in case of jQuery/JS, when enhancing the header controls from the calenar view,
     * and we pass the calendar view instance, which is ready, instead of the host instance, which does not have
     * the calendar view instance yet in the initial render.
     */
    view?: CalendarViewBase;
    [key: string]: any;
}
export declare class InstanceSubscriber extends PureComponent<IInstanceSubscriberProps, any> {
    private _handler;
    private _changes?;
    componentWillUnmount(): void;
    render(): import("../../../preact/lib/src").VNode<any>;
}
export interface ICalendarHeaderProp {
    calendar?: ICalendarViewHost;
    className?: string;
    view?: CalendarViewBase;
}
export declare const CalendarPrev: {
    ({ calendar, view, ...others }: ICalendarHeaderProp): createElement.JSX.Element;
    _name: string;
};
export declare const CalendarNext: {
    ({ calendar, view, ...others }: ICalendarHeaderProp): createElement.JSX.Element;
    _name: string;
};
export declare const CalendarToday: {
    ({ calendar, view, ...others }: ICalendarHeaderProp): createElement.JSX.Element;
    _name: string;
};
export declare const CalendarNav: {
    ({ calendar, view, ...others }: ICalendarHeaderProp): createElement.JSX.Element;
    _name: string;
};
