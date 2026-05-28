<header class="header">
    <h2 class="u-name"><b>E</b>.T.M.S
        <label for="checkbox">
            <i id="navbtn" class="fa-solid fa-bars"></i>
        </label>
    </h2>
    
    <span class="notification"  id="notificationBtn">
        <i class="fa-solid fa-bell"></i>
        <span id="notificationNum"></span>
    </span>
</header>

<div class="notification-bar" id="notificationBar">
    <ul id="notifications">

    </ul>
</div>

<script type="text/javascript">
    var openNotification = false;

    const notification = ()=> {
        let notificationBar = document.querySelector("#notificationBar");
        if (openNotification) {
            notificationBar.classList.remove('open-notification')
            openNotification = false;
        } else {
            notificationBar.classList.add('open-notification')
            openNotification = true;

        }
    }
    let notificationBtn =  document.querySelector("#notificationBtn");
    notificationBtn.addEventListener('click', notification);
</script>

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>

<script tabindex="text/javascript">
    $(document).ready(function() {
        
        $("#notificationNum").load("app/notifications-count.php");
        $("#notifications").load("app/notifications.php");
    });


</script>