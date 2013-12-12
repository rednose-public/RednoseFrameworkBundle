var formCalendar = {
    Y: null,
    properties: {},

    renderCalendar: function () {
        var Y = formCalendar.Y;

        Y.all('.formControl_calendar').each(function() {
            var calendarTimestamp = this.get('parentNode').one('input[type=hidden]');
            var calendarContainer = Y.Node.create('<div class="calendarContainer" />');
            var calendarCheckbox = Y.Node.create('<input style="display: none;" type="checkbox" checked="checked" class="calendarCheckbox" />');
            var calendarInput = Y.Node.create('<div class="calendarInput" />');

            if (calendarTimestamp == null) {
                calendarTimestamp = Y.Node.create('<div />');
                calendarTimestamp.hide();
            }

            if (this.hasClass('calendarMutex')) {
                return;
            }
            this.addClass('calendarMutex');

            var calendar = new Y.Calendar({
                srcNode: this,
                height: '230px',
                width: '205px',
                showPrevMonth: true,
                showNextMonth: true,
                date: new Date()
            }).render();

            var calendarNode = Y.one('#' + calendar.get('id'));

            calendarNode.addClass('calendarObject');

            // Hack: CSS3 fix
            calendarNode.one('span').setStyle('float', 'left');

            with (calendarContainer) {
                insert(calendarContainer, calendarNode);

                append(calendarTimestamp);
                append(calendarCheckbox);
                append(calendarInput);
                append(calendarNode);
            }

            formCalendar.loadProperties(this, calendarContainer);

            calendarInput.on('click', function() {
                formCalendar.showCalendar(calendar, calendarInput);
            });

            calendar.on('selectionChange', function (clickEvent) {
                formCalendar.calendarSelect(clickEvent, calendar, calendarTimestamp, calendarContainer);
            });

            calendarCheckbox.on('change', function() {
                formCalendar.changeState(calendarCheckbox.get('checked'), calendarInput, calendarTimestamp);
            });

            calendar.hide();

            if (formCalendar.properties['disableable']) {
                formCalendar.enableCheckbox(calendarContainer, formCalendar.properties['disableable']);
            }
        });
    },

    loadProperties: function (parent, container) {
        var Y = formCalendar.Y;
        var value = parent.getAttribute('data-value');

        formCalendar.properties['disableable'] = (parent.getAttribute('data-disableable') == 'true');

        container.one('.calendarInput').set(
            'innerHTML',
            Y.DataType.Date.format(new Date(value * 1000), {format: '%x'})
        );
    },

    calendarSelect: function (clickEvent, calendar, calendarTimestamp, container) {
        var Y = formCalendar.Y;
        var selectedDate = clickEvent.newSelection[0];

        container.one('.calendarInput').set('innerHTML',
            Y.DataType.Date.format(selectedDate, {format: '%x'})
        );
        calendarTimestamp.set('value', Math.round(selectedDate.getTime() / 1000));

        calendar.hide();

        Y.one('#' + calendar.get('id') + '_mask').remove();
    },

    changeState: function (enabled, calendarInput) {
        if (enabled) {
            calendarInput.show();
        } else {
            calendarInput.hide();
        }
    },

    showCalendar: function (calendar, calendarInput) {
        var Y = formCalendar.Y;
        var calendarNode = Y.one('#' + calendar.get('id'));
        var mask = Y.Node.create('<div id="' + calendar.get('id') + '_mask" />');

        // Setting z-Index in yui3 has issues.
        calendarNode.getDOMNode().style.zIndex = (Y.all('*').size() + 2);

        mask.setStyles({
            position: 'fixed', width: '100%', height: '100%', top: '0', left: '0', display: 'block',
            zIndex: Y.all('*').size()
        });

        mask.on('click', function() {
            calendar.hide();
            mask.remove();
        });

        calendarNode.insert(mask, calendarNode);
        calendarNode.setStyle(
            'left',
            calendarInput.getXY()[0] - calendarInput.get('parentNode').getXY()[0]
        );
        calendarNode.setStyle('top', calendarInput.getStyle('height'));

        calendar.show();
    },

    enableCheckbox: function(calendarContainer, disable) {
        var checkbox = calendarContainer.one('.calendarCheckbox');

        if (disable) {
            checkbox.setStyle('display', 'block');
        } else {
            checkbox.setStyle('display', 'none');
        }
    }
};
