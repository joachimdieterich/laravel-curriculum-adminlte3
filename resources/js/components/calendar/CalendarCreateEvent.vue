<template>
  <div>
      <p>Wann möchtest du üben?</p>
        <div class="btn btn-primary pointer">
          <i class="fas fa-add pr-1"></i>
          {{ trans('global.calendarEvent.create') }}
        </div>
  </div>
</template>
<script>
export default {
  name: 'CalendarCreateEvent'
}
</script>
