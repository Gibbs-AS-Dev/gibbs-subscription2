import { Eventcalendar as EventcalendarComponent } from '../../core/components/eventcalendar/eventcalendar.common';
export * from '../../core/components/eventcalendar/eventcalendar.public';
export * from '../shared/calendar-header';
export * from './draggable';
declare class Eventcalendar extends EventcalendarComponent {
    static _fname: string;
    static _renderOpt: import("../../preact/renderer").IRenderOptions;
}
export { Eventcalendar };
