import { Controller } from 'stimulus';
import { Calendar } from '@fullcalendar/core';
import listPlugin from '@fullcalendar/list';

export default class extends Controller {
    connect() {
        var calendar = new Calendar(this.element, {
            plugins: [ listPlugin ],
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            initialView: 'listWeek',
            events: '/appointments',
            eventClick: arg => {
                const event = new CustomEvent("booking-slot-selected", {
                    detail: arg
                });
                window.dispatchEvent(event);
                console.debug("sent event", event);
            },
            eventDataTransform: json => {
                return {
                    ...json,
                    title: `${json.bookings.length} / ${json.capacity} ${json.title}`,
                };
            }
        });

        calendar.render();
    }
}
