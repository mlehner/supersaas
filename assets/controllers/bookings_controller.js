import { Controller } from 'stimulus';
import { formatRange } from '@fullcalendar/core';

export default class extends Controller {
    connect() {
        this.element.innerText = 'Select a timeslot.';
    }

    displayBookings(domEvent) {
        console.debug("received event", domEvent);
        const calendarSlot = domEvent.detail;

        const bookingsContent = [];

        const slotHeader = document.createElement('h2');
        slotHeader.innerText = calendarSlot.event.title;
        bookingsContent.push(slotHeader);

        const dateHeader = document.createElement('h3');
        dateHeader.innerText = formatRange(calendarSlot.event.start, calendarSlot.event.end, {
            month: 'long',
            year: 'numeric',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
        });
        bookingsContent.push(dateHeader);

        const bookings = calendarSlot.event.extendedProps.bookings;

        if (bookings.length > 0) {
            const list = document.createElement('ul');

            for (const booking of bookings) {
                const bookingElement = document.createElement('li');
                bookingElement.innerText = booking.full_name;

                list.appendChild(bookingElement);
            }

            bookingsContent.push(list);
        } else {
            const noBookings = document.createElement('p');
            noBookings.innerText = 'No bookings.';
            bookingsContent.push(noBookings)
        }

        this.element.replaceChildren(...bookingsContent);
    }
}
