import { MbscDatepickerOptions } from './components/datepicker';
import { MbscDraggableOptions, MbscEventcalendarOptions } from './components/eventcalendar';
import { MbscButtonOptions, MbscCheckboxOptions, MbscInputOptions, MbscPageOptions, MbscRadioOptions, MbscSegmentedOptions, MbscStepperOptions, MbscSwitchOptions } from './components/forms';
import { MbscPopupOptions } from './components/popup';
import { MbscSelectOptions } from './components/select';
export * from '../core/bundle';
export * from './components/datepicker';
export * from './components/select';
export * from './components/eventcalendar';
export * from './components/forms';
export * from './components/popup';
import { registerComponent } from './base';
export { registerComponent };
export { enhance, getInst } from '../preact/renderer';
declare global {
    interface MobiscrollBundle {
        [index: number]: JQuery;
        select(options?: MbscSelectOptions): JQuery;
        datepicker(options?: MbscDatepickerOptions): JQuery;
        eventcalendar(options?: MbscEventcalendarOptions): JQuery;
        popup(options?: MbscPopupOptions): JQuery;
        button(options?: MbscButtonOptions): JQuery;
        checkbox(options?: MbscCheckboxOptions): JQuery;
        draggable(options?: MbscDraggableOptions): JQuery;
        input(options?: MbscInputOptions): JQuery;
        dropdown(options?: MbscInputOptions): JQuery;
        textarea(options?: MbscInputOptions): JQuery;
        page(options?: MbscPageOptions): JQuery;
        radio(options?: MbscRadioOptions): JQuery;
        segmented(options?: MbscSegmentedOptions): JQuery;
        stepper(options?: MbscStepperOptions): JQuery;
        switch(options?: MbscSwitchOptions): JQuery;
    }
    interface JQuery {
        mobiscroll(): MobiscrollBundle;
        mobiscroll(option: string, ...params: any[]): any;
    }
}
