<style>
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
    padding: 0 24px;
    background-color: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    font-family: 'Segoe UI', Arial, sans-serif;
    position: sticky;
    top: 0;
    z-index: 1020;
}

.topbar-left {
    display: flex;
    align-items: center;
}

.menu-icon {
    width: 22px;
    height: 22px;
    cursor: pointer;
    color: #374151;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.topbar-date {
    font-size: 14px;
    color: #374151;
}

.notification-wrapper {
    position: relative;
    cursor: pointer;
}

.notification-icon {
    width: 20px;
    height: 20px;
    color: #4b5563;
}

.notification-dot {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background-color: #f59e0b;
    border-radius: 50%;
    border: 2px solid #ffffff;
}

.avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #1e3a8a;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
</style>

<div class="topbar">
    <div class="topbar-left">
    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</div>

<div class="topbar-right">
    <span class="topbar-date">
        <?php
            date_default_timezone_set("America/Bogota");
            $formatter = new IntlDateFormatter(
                'es_ES',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                'America/Bogota',
                IntlDateFormatter::GREGORIAN,
                "EEEE, d 'de' MMMM 'de' y"
            );
            echo $formatter->format(new DateTime());
        ?>
    </span>

    <div class="notification-wrapper">
        <svg class="notification-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span class="notification-dot"></span>
    </div>

    <div class="avatar">AR</div>
    </div>
</div>