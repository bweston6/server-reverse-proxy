---
title: Home Assistant Automated Blinds Build Log
author: Ben Weston
date: '2025-06-03T14:57+01:00'
modified: '2025-06-03T14:57+01:00'
---

<?php require_once ROOT_DIR . "/partials/image.php"; ?>

# Home Assistant Automated Blinds Build Log

<dl class="article-dates inline">
<dt>Written</dt>
<dd><time datetime="2025-06-03T14:57+01:00">2025-06-03T14:57+01:00</time></dd>
</dl>

Living in a flat in the middle of a city has its perks but having neighbours within throwing distance of all your windows is not one of them. For better or worse, opening and closing blinds is an essential part of my day but one which can be automated!

The goals of this project are as follows:

* Integrate with my existing Home Assistant instance for easy remote control and scheduling.
* Make use of the stepper motor drivers that have been sitting in my parts drawer for too long.
* Have as many parts 3D printable as possible.

## The Mock-up
### Electronics
<?= image("/assets/breadboard.avif", "Breadboard with a barrel jack connected to the power rails on flying leads. From left to right there's a Raspberry Pi Pico W, A4988 stepper motor driver and L78 series linear regulator."); ?>

To get things started I laid out some components on a breadboard so I could get the stepper motor moving. The brains of the project is a Raspberry Pi Pico W running [ESPHome](https://esphome.io/components/rp2040.html). This is connected up to a cheap A4988 stepper motor driver powered by a 24v supply.

<?= image("/assets/nema-17.avif", "Nema 17 stepper motor", "width-50"); ?>

To step down the 24v to 5v, for the Pico, I used a simple L78 linear regulator. This method of stepping down voltage is terribly inefficient as the difference between the supply and output voltage is bled off as heat. 

$$
P_\text{waste} = (V_\text{in}-V_\text{out})\cdot I_\text{out}
$$

This means that given a peak power draw of maybe 100mA for the Pico and A4988 combined, the little regulator would be dissipating 1.9 Watts. The regulator has protections to ensure that is doesn't overheat but it would definitely require a heat sink for the final build.

### Software
Next is configuration to enable ESPHome and Home Assistant to interact with the motor. ESPHome provides a [Docker container](https://hub.docker.com/r/esphome/esphome) that can generate firmware for your microcontroller based on yaml files for configuration. After some hacking I had something like this:

<?= image("/assets/esphome.avif", "ESPhome online configuration editor showing the start of the blinds configuration."); ?>

The [stepper component](https://esphome.io/components/stepper/) makes it super easy to use stepper motors with ESPHome by only defining a couple of GPIO pins. However, Home Assistant can't directly control stepper motors so we need to expose it as a [cover](https://www.home-assistant.io/integrations/cover/):

```yml
cover:
  - platform: template
    id: blinds_cover
    name: "Roller Blind"
    device_class: shade
    ...
    position_action: 
      then:
        - stepper.set_target:
            id: blinds_stepper
            target: !lambda 'return (int)((1 - pos) * id(max_steps));'
    ...
```

Here I'm using a [template](https://esphome.io/automations/templates.html) to expose a function that Home Assistant can run on the microcontroller. More specifically I'm using the [cover template](https://esphome.io/components/cover/template.html). This allows Home Assistant to show a nice interface to allow precise control of the blind, including buttons to open, close and stop the thing too.

<div style="display: flex; flex-wrap: wrap; gap: 1rem;">
<?= image("/assets/home-assistant-slider.avif", "Exact positioning slider from the Home Assistant web interface.", "width-50"); ?>

<?= image("/assets/home-assistant-buttons.avif", "Up, down, and stop buttons for blinds in the Home Assistant web interface.", "width-50"); ?>
</div>

### Gearing
My blinds are huge and heavy, even with the maximum current the A4988 can provide they won't budge under just the power of the stepper motor. To get an output with enough torque to move them I'm using this [5:1 planetary gearbox](https://www.printables.com/model/44974-nema-17-stepper-51-planetary-gearbox-with-5mm-shaf) that a co-worker recommended when he got earshot of my issues. 

<?= image("/assets/gearbox-side.avif", "Printed gearbox attached to the NEMA 17 stepper motor."); ?>

<?= image("/assets/gearbox-top.avif", "View from the top showing the gear meshing with the blind's bead chain."); ?>

It wasn't a long print and after assembling using the parts list in the description, the operation is smooth and quiet. With the new gearbox mounted the blinds are moving just fine and it's not such a strong reduction that it can't be back driven. 

## The Installation
To attach the assembly to the wall I remixed this [mount designed to motorise bead blinds](https://www.thingiverse.com/thing:5965826) to fit where the plastic loop holding the blind beads was screwed to the wall. However, once again my very heavy blinds strike back! The beads don't have enough grip against the gear from the model, so I further modified the design to fit a gear that completely surrounds each bead to grip it. With that the mechanical interface to the blinds is complete, but a breadboard isn't permanent enough for something I want to leave plugged in 24hrs a day. 

This is the schematic for the final circuit which I built up onto some strip board I had lying around:

<img src="/assets/blinds-schematic.svg" style="aspect-ratio: 794 / 559" alt="Circuit board schematic.">

I did contemplate having a PCB made up, but for a one-off prototype I prefer the convenience of having it made in an evening. It certainly brought back some school memories of using a drill bit to break the strips.

<?= image("/assets/strip-board-front.avif", "The front of the strip board"); ?>

<?= image("/assets/strip-board-back.avif", "The back of the strip board"); ?>

I measured up the board and designed a small box that can hide everything out the way. It's all well and good being able to automate your blinds or control them through your phone but that isn't so intuitive if we have people round. To that end I configured a [SOMRIG Zigbee button](https://www.ikea.com/gb/en/p/rodret-wireless-dimmer-power-switch-smart-white-80559796/) and printed off a custom face place to give some hints for what it is for. A few people have made some excellent resources and I used [an existing faceplate model](https://www.printables.com/model/939679-ikea-rodret-somrig-replacement-faceplate) in combination with [Aasikki's button template](https://github.com/Aasikki/IKEA-Button-Icon-Templates/blob/2f62384b2467562950fe503f9526b531f2ab9581/IKEA%20Rodret%20and%20Somrig%20Template.pdf) to create a reasonable design using Autodesk Fusion. 

<?= image("/assets/rodret.avif", "A render of the custom button faceplate with options for opening blinds at the top, closing on the bottom left and stopping on the bottom right."); ?>

With that I'm rather pleased with how the whole installation came out. The only remaining issue is that the whole system operates by dead-reckoning. Occasionally the stepper motor slips or there's some delay in ESPHome which causes the position of the blind's open and closed states to drift out of sync with reality. I've made a number input, in Home Assistant, that allows me to trim the blinds back into position if it becomes an issue but after several months I haven't had to use it often enough to be a problem. The only real problem now is that I've got a project shaped hole in my evenings that will inevitably be filled with Clarkson's Farm and Taskmaster. I'll just have to wait until another idea crosses my mind.

## Models & Configuration
You can access the configuration required to make this project work on [my GitHub](https://github.com/bweston6). I haven't posted the 3D models yet but if you want them before they're up, give me a nudge on [blinds@bweston.uk](mailto:blinds@bweston.uk).
