<?php require_once ROOT_DIR . "/partials/image.php"; ?>

<h1 id="home-assistant-automated-blinds-build-log">Home Assistant
Automated Blinds Build Log</h1>
<dl class="article-dates inline">
<dt>Written</dt>
<dd><time datetime="2025-06-03T14:57+01:00">2025-06-03T14:57+01:00</time></dd>
</dl>

<p>Living in a flat in the middle of a city has its perks but having
neighbours within throwing distance of all your windows is not one of
them. For better or worse, opening and closing blinds is an essential
part of my day but one which can be automated!</p>
<p>The goals of this project are as follows:</p>
<ul>
<li>Integrate with my existing Home Assistant instance for easy remote
control and scheduling.</li>
<li>Make use of the stepper motor drivers that have been sitting in my
parts drawer for too long.</li>
<li>Have as many parts 3D printable as possible.</li>
</ul>
<h2 id="the-mock-up">The Mock-up</h2>
<h3 id="electronics">Electronics</h3>
<?= image("/assets/breadboard.avif", "Breadboard with a barrel jack connected to the power rails on flying leads. From left to right there's a Raspberry Pi Pico W, A4988 stepper motor driver and L78 series linear regulator."); ?>

<p>To get things started I laid out some components on a breadboard so I
could get the stepper motor moving. The brains of the project is a
Raspberry Pi Pico W running <a
href="https://esphome.io/components/rp2040.html">ESPHome</a>. This is
connected up to a cheap A4988 stepper motor driver powered by a 24v
supply.</p>
<?= image("/assets/nema-17.avif", "Nema 17 stepper motor", "width-50"); ?>

<p>To step down the 24v to 5v, for the Pico, I used a simple L78 linear
regulator. This method of stepping down voltage is terribly inefficient
as the difference between the supply and output voltage is bled off as
heat.</p>
<p><math display="block" xmlns="http://www.w3.org/1998/Math/MathML"><semantics><mrow><msub><mi>P</mi><mtext mathvariant="normal">waste</mtext></msub><mo>=</mo><mrow><mo stretchy="true" form="prefix">(</mo><msub><mi>V</mi><mtext mathvariant="normal">in</mtext></msub><mo>−</mo><msub><mi>V</mi><mtext mathvariant="normal">out</mtext></msub><mo stretchy="true" form="postfix">)</mo></mrow><mo>⋅</mo><msub><mi>I</mi><mtext mathvariant="normal">out</mtext></msub></mrow><annotation encoding="application/x-tex">
P_\text{waste} = (V_\text{in}-V_\text{out})\cdot I_\text{out}
</annotation></semantics></math></p>
<p>This means that given a peak power draw of maybe 100mA for the Pico
and A4988 combined, the little regulator would be dissipating 1.9 Watts.
The regulator has protections to ensure that is doesn't overheat but it
would definitely require a heat sink for the final build.</p>
<h3 id="software">Software</h3>
<p>Next is configuration to enable ESPHome and Home Assistant to
interact with the motor. ESPHome provides a <a
href="https://hub.docker.com/r/esphome/esphome">Docker container</a>
that can generate firmware for your microcontroller based on yaml files
for configuration. After some hacking I had something like this:</p>
<?= image("/assets/esphome.avif", "ESPhome online configuration editor showing the start of the blinds configuration."); ?>

<p>The <a href="https://esphome.io/components/stepper/">stepper
component</a> makes it super easy to use stepper motors with ESPHome by
only defining a couple of GPIO pins. However, Home Assistant can't
directly control stepper motors so we need to expose it as a <a
href="https://www.home-assistant.io/integrations/cover/">cover</a>:</p>
<div class="sourceCode" id="cb1"><pre
class="sourceCode yml"><code class="sourceCode yaml"><span id="cb1-1"><a href="#cb1-1" aria-hidden="true" tabindex="-1"></a><span class="fu">cover</span><span class="kw">:</span></span>
<span id="cb1-2"><a href="#cb1-2" aria-hidden="true" tabindex="-1"></a><span class="at">  </span><span class="kw">-</span><span class="at"> </span><span class="fu">platform</span><span class="kw">:</span><span class="at"> template</span></span>
<span id="cb1-3"><a href="#cb1-3" aria-hidden="true" tabindex="-1"></a><span class="at">    </span><span class="fu">id</span><span class="kw">:</span><span class="at"> blinds_cover</span></span>
<span id="cb1-4"><a href="#cb1-4" aria-hidden="true" tabindex="-1"></a><span class="at">    </span><span class="fu">name</span><span class="kw">:</span><span class="at"> </span><span class="st">&quot;Roller Blind&quot;</span></span>
<span id="cb1-5"><a href="#cb1-5" aria-hidden="true" tabindex="-1"></a><span class="at">    </span><span class="fu">device_class</span><span class="kw">:</span><span class="at"> shade</span></span>
<span id="cb1-6"><a href="#cb1-6" aria-hidden="true" tabindex="-1"></a><span class="at">    ...</span></span>
<span id="cb1-7"><a href="#cb1-7" aria-hidden="true" tabindex="-1"></a><span class="at">    </span><span class="fu">position_action</span><span class="kw">:</span><span class="at"> </span></span>
<span id="cb1-8"><a href="#cb1-8" aria-hidden="true" tabindex="-1"></a><span class="at">      </span><span class="fu">then</span><span class="kw">:</span></span>
<span id="cb1-9"><a href="#cb1-9" aria-hidden="true" tabindex="-1"></a><span class="at">        </span><span class="kw">-</span><span class="at"> </span><span class="fu">stepper.set_target</span><span class="kw">:</span></span>
<span id="cb1-10"><a href="#cb1-10" aria-hidden="true" tabindex="-1"></a><span class="at">            </span><span class="fu">id</span><span class="kw">:</span><span class="at"> blinds_stepper</span></span>
<span id="cb1-11"><a href="#cb1-11" aria-hidden="true" tabindex="-1"></a><span class="at">            </span><span class="fu">target</span><span class="kw">:</span><span class="at"> !lambda &#39;return (int)((1 - pos) * id(max_steps));&#39;</span></span>
<span id="cb1-12"><a href="#cb1-12" aria-hidden="true" tabindex="-1"></a><span class="at">    ...</span></span></code></pre></div>
<p>Here I'm using a <a
href="https://esphome.io/automations/templates.html">template</a> to
expose a function that Home Assistant can run on the microcontroller.
More specifically I'm using the <a
href="https://esphome.io/components/cover/template.html">cover
template</a>. This allows Home Assistant to show a nice interface to
allow precise control of the blind, including buttons to open, close and
stop the thing too.</p>
<div style="display: flex; flex-wrap: wrap; gap: 1rem;">
<?= image("/assets/home-assistant-slider.avif", "Exact positioning slider from the Home Assistant web interface.", "width-50"); ?>

<?= image("/assets/home-assistant-buttons.avif", "Up, down, and stop buttons for blinds in the Home Assistant web interface.", "width-50"); ?>

</div>

<h3 id="gearing">Gearing</h3>
<p>My blinds are huge and heavy, even with the maximum current the A4988
can provide they won't budge under just the power of the stepper motor.
To get an output with enough torque to move them I'm using this <a
href="https://www.printables.com/model/44974-nema-17-stepper-51-planetary-gearbox-with-5mm-shaf">5:1
planetary gearbox</a> that a co-worker recommended when he got earshot
of my issues.</p>
<?= image("/assets/gearbox-side.avif", "Printed gearbox attached to the NEMA 17 stepper motor."); ?>

<?= image("/assets/gearbox-top.avif", "View from the top showing the gear meshing with the blind's bead chain."); ?>

<p>It wasn't a long print and after assembling using the parts list in
the description, the operation is smooth and quiet. With the new gearbox
mounted the blinds are moving just fine and it's not such a strong
reduction that it can't be back driven.</p>
<h2 id="the-installation">The Installation</h2>
<p>To attach the assembly to the wall I remixed this <a
href="https://www.thingiverse.com/thing:5965826">mount designed to
motorise bead blinds</a> to fit where the plastic loop holding the blind
beads was screwed to the wall. However, once again my very heavy blinds
strike back! The beads don't have enough grip against the gear from the
model, so I further modified the design to fit a gear that completely
surrounds each bead to grip it. With that the mechanical interface to
the blinds is complete, but a breadboard isn't permanent enough for
something I want to leave plugged in 24hrs a day.</p>
<p>This is the schematic for the final circuit which I built up onto
some strip board I had lying around:</p>
<img src="/assets/blinds-schematic.svg" style="aspect-ratio: 794 / 559" alt="Circuit board schematic.">

<p>I did contemplate having a PCB made up, but for a one-off prototype I
prefer the convenience of having it made in an evening. It certainly
brought back some school memories of using a drill bit to break the
strips.</p>
<?= image("/assets/strip-board-front.avif", "The front of the strip board"); ?>

<?= image("/assets/strip-board-back.avif", "The back of the strip board"); ?>

<p>I measured up the board and designed a small box that can hide
everything out the way. It's all well and good being able to automate
your blinds or control them through your phone but that isn't so
intuitive if we have people round. To that end I configured a <a
href="https://www.ikea.com/gb/en/p/rodret-wireless-dimmer-power-switch-smart-white-80559796/">SOMRIG
Zigbee button</a> and printed off a custom face place to give some hints
for what it is for. A few people have made some excellent resources and
I used <a
href="https://www.printables.com/model/939679-ikea-rodret-somrig-replacement-faceplate">an
existing faceplate model</a> in combination with <a
href="https://github.com/Aasikki/IKEA-Button-Icon-Templates/blob/2f62384b2467562950fe503f9526b531f2ab9581/IKEA%20Rodret%20and%20Somrig%20Template.pdf">Aasikki's
button template</a> to create a reasonable design using Autodesk
Fusion.</p>
<?= image("/assets/rodret.avif", "A render of the custom button faceplate with options for opening blinds at the top, closing on the bottom left and stopping on the bottom right."); ?>

<p>With that I'm rather pleased with how the whole installation came
out. The only remaining issue is that the whole system operates by
dead-reckoning. Occasionally the stepper motor slips or there's some
delay in ESPHome which causes the position of the blind's open and
closed states to drift out of sync with reality. I've made a number
input, in Home Assistant, that allows me to trim the blinds back into
position if it becomes an issue but after several months I haven't had
to use it often enough to be a problem. The only real problem now is
that I've got a project shaped hole in my evenings that will inevitably
be filled with Clarkson's Farm and Taskmaster. I'll just have to wait
until another idea crosses my mind.</p>
<h2 id="models--configuration">Models &amp; Configuration</h2>
<p>You can access the configuration required to make this project work
on <a href="https://github.com/bweston6">my GitHub</a>. I haven't posted
the 3D models yet but if you want them before they're up, give me a
nudge on <a
href="mailto:blinds@bweston.uk"><span>blinds@bweston.uk</span></a>.</p>
