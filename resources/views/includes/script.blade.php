<!-- jQuery harus di-load terlebih dahulu -->
<script src="{{ asset('assets/plugins/jquery/jquery-3.4.1.min.js') }}"></script>

<!-- Library eksternal lainnya -->
<script defer src="https://unpkg.com/@popperjs/core@2"></script>
<script defer src="https://unpkg.com/feather-icons"></script>

<!-- Bootstrap -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- Plugin lainnya -->
<script src="{{ asset('assets/plugins/perfectscroll/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/plugins/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/apexcharts/apexcharts.min.js') }}"></script>

<!-- Main Script -->
<script src="{{ asset('assets/js/main.min.js') }}"></script>

<!-- Halaman spesifik -->
<script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
<script src="{{ asset('assets/js/pages/datatables.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();
    });

    document.addEventListener("DOMContentLoaded", function() {
        const ps = new PerfectScrollbar('.your-scroll-container');
    });
</script>

<script>
$(document).ready(function () {
    function fetchNotifications() {
        $.ajax({
            url: "{{ route('notifications.get') }}",
            type: "GET",
            dataType: "json",
            success: function (data) {
                let notifDropdown = $(".notif-drop-menu");
                notifDropdown.html('<h6 class="dropdown-header">Notifications</h6>');

                if (data.length === 0) {
                    notifDropdown.append('<p class="text-center p-2">No notifications</p>');
                } else {
                    data.forEach(function (notif) {
                        let createdAt = new Date(notif.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        let notifItem = `
                            <a href="${notif.type === 'reschedule' ? '{{ route('reschedule.index') }}' : '{{ route('schedule.index') }}'}">
                                <div class="header-notif">
                                    <div class="notif-text">
                                        <p class="bold-notif-text">${notif.title}</p>
                                        <small>${notif.message}</small>
                                        <br>
                                        <small class="text-muted">${createdAt}</small>
                                    </div>
                                </div>
                            </a>`;
                        notifDropdown.append(notifItem);
                    });
                }

                $(".notifications-dropdown .badge").text(data.length);
            }
        });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 10000);
});
</script>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    // Aktifkan log untuk memeriksa komunikasi Pusher di konsol
    Pusher.logToConsole = true;

    // Inisialisasi Pusher
    var pusher = new Pusher('2d59d7b8156ef1107a27', {
        cluster: 'ap1'
    });

    // Subscribe ke channel 'notification-channel'
    var channel = pusher.subscribe('notification-channel');

    // Memeriksa apakah browser mendukung notifikasi
    if ('Notification' in window) {
        // Memeriksa apakah pengguna sudah memberikan izin
        if (Notification.permission !== "granted") {
            Notification.requestPermission().then(function(permission) {
                if (permission === "granted") {
                    console.log("Izin notifikasi diberikan");
                }
            });
        }
    } else {
        console.log("Browser tidak mendukung notifikasi");
    }

    // Menangani event dari Pusher dan menampilkan notifikasi
    channel.bind('NotificationSent', function(data) {
        console.log(data); // Periksa apakah data diterima dengan benar

        // Memeriksa izin notifikasi dan menampilkan notifikasi
        if (Notification.permission === 'granted') {
            var notification = new Notification('New notification', {
                body: data.message, // Isi pesan dari Pusher
                icon: 'https://via.placeholder.com/150' // Gambar icon notifikasi
            });

            // Jika notifikasi diklik, buka halaman tertentu
            notification.onclick = function() {
                window.location.href = 'https://your-website-url.com'; // Ganti dengan URL yang sesuai
            };
        }
    });
</script>
