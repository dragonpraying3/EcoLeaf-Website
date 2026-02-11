# EcoLeaf 🌱

EcoLeaf is a **challenge-based sustainability web platform** designed for the APU campus. It tracks user participation in green activities, rewards sustainable behavior, and visualizes measurable environmental impact through points, badges, analytics dashboards, and reports.

The system supports **Students, Organizers, and Admins**, each with role-specific features and dashboards.

---

## 📍 For Monitor and Test User Account
* Student

username: studentA 
password: password@123

* Organizer

username: orgA 
password: password@123

* Admin

username: adminA 
password: password@123

---

## 📍 System Scope

* **Location**: Fixed to APU Campus
* **Purpose**: Track user progress, encourage sustainable actions, and visualize environmental impact
* **Core Concepts**: Points, Rewards, Badges, Attendance, Analytics

---

## 👥 User Roles

### Student

* Join events and mark attendance
* Earn Leaf(green points) and badges
* Redeem rewards
* View personal analytics dashboard
* Participate in DIY Hub trading
* View leaderboard
* Calculate carbon effort 

### Organizer

* Create and manage events
* Mark attendance and generate OTP
* Submit post-event summaries
* View organizer-level analytics
* Calculate carbon effort 

### Admin

* Approve/reject events
* Manage rewards stock and points
* Manage DIY Hub post request
* Configure point distribution
* Manage badges (hide/unhide)
* View overall system analytics
* Calculate carbon effort 

---

## 🏆 Reward System

### Reward Flow

1. Rewards have **limited stock** managed by admin
2. When a student clicks **REWARD**:

   * Item stock decreases by **-1**
   * Student points are **deducted**
   * One-time redeem logic 
3. If stock = 0 AND status = inactive:

   * Reward button becomes **disabled automatically**
   * Button re-enabled only after admin updates stock
   * The item is still display in queue but is not clickable 
4. If stock > 1 AND status = inactive:

   * The item will not clickable

---

## 🎖️ Badge System

### Badge Unlock Rules

* Badges are unlocked based on **fixed point thresholds**
* Example:

  * `1 DIY Hub Post → EcoBeginner Badge`
* Badge rules are **permanent** (not flexible with future point changes)

### Badge Management

* Admin can **Hide / Delete** badge
* Effects:

  * Existing users **keep the badge**
  * New users **cannot earn** the hidden badge
  * Deleted badge will be permanently delete

---

## 📅 Attendance & Event Participation

### Attendance Process

1. Event is approved → appears in **Student My Event page**
2. Organizer clicks **MARK ATTENDANCE**

   * System generates an **OTP code**
   * OTP is stored in the database
3. Student clicks **MARK ATTENDANCE**

   * Enters OTP
   * Event ID + OTP are verified
4. If valid:

   * Attendance marked
   * Points automatically awarded
   * Attendance button becomes invisible
   * Button text changes to **"Event Ended"**

---

## 📝 Post-Event Summary (Organizer)

After **COMPLETE EVENT**, organizer submits a summary via hidden block:

### Post-Event Data Collected

* Total green plants planted
* Total waste collected
* Total recycled items
* Total participants (auto-count from attendance table)

### Effects

* Data updates organizer dashboard
* Data updates admin dashboard

---

## 🎯 Points Distribution

* Points are distributed **automatically** after attendance is marked
* Distribution value is configurable by admin
* Points are divided **without decimals**

---

## 🔔 Notification System

Notifications are used to communicate status updates between roles:

### Notification Examples

* Organizer → Student: event rejection reason
* Admin → Organizer: event rejection reason
* Organizer → Student: point distribution completed

---

## 📊 Analytical Dashboards (Measurable Data)

Each role has a different dashboard with real-time metrics.

---

## 🔁 DIY Hub Trading System

### Trading Flow

1. Buyer clicks **TRADE** → popup form appears
2. Buyer selects:

   * Location
   * Time range
3. Trade request sent to seller
4. Seller must **ACCEPT / REJECT** within **24 hours**

   * If no response → post becomes visible again

### Trade Completion Rules

* Both buyer and seller must click **COMPLETE**
* When confirmed:

  * Trade is successful
  * Post becomes invisible

### Time Logic

* `startTime`: selected by user (datetime)
* `endTime = startTime + 2 hours`
* If `endTime + 2 hours` and not completed:

  * Post becomes visible again

---

## 🌍 Carbon Footprint Calculator

* Users input daily activity data
* System calculates carbon footprint
* Personalized advice generated based on result

---

## 🏅 Leaderboard (Student Only)

* Displays:

  * Username
  * Total points
  * Position
* Used to motivate competition and engagement

---

## 📌 Summary

EcoLeaf integrates **rewards, badges, attendance, analytics, and trading** into a single sustainability platform. The system emphasizes **measurable impact**, **progress tracking**, and **user engagement** to promote greener behavior within the APU campus.

---

**Project Name:** EcoLeaf
**Type:** Sustainability Challenge Website
**Focus:** Measurable Green Impact & User Progress Tracking
