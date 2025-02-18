<ul class="list-unstyled accordion-menu">
    <li class="sidebar-title">
      Main
    </li>
    <li class="active-page">
      <a href="index.html"><i data-feather="home"></i>Dashboard</a>
    </li>
    <li class="sidebar-title">
      Apps
    </li>
    <li class="{{ request()->is('location') ? 'active-page' : '' }}">
        <a href="{{route('location.index')}}"><i data-feather="map-pin"></i>Location</a>
    </li>
    <li class="{{ request()->is('coach') ? 'active-page' : '' }}">
        <a href="{{route('coach.index')}}"><i data-feather="user"></i>Coach</a>
    </li>
    <li class="{{ request()->is('mastercoach') ? 'active-page' : '' }}">
        <a href="{{route('mastercoach.index')}}"><i data-feather="user-plus"></i>Master Coach</a>
    </li>
    {{-- <li class="">
        <a href=""><i data-feather="user-plus"></i>Master Coach</a>
    </li> --}}
    <li class="{{ request()->is('schedule') ? 'active-page' : '' }}">
        <a href="{{route('schedule.index')}}"><i data-feather="calendar"></i>Calendar</a>
      </li>

  </ul>
