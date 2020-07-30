<?php
/**
 * The Context defines the interface of interest to clients. It also maintains a
 * reference to an instance of a State subclass, which represents the current
 * state of the Context.
 */
class Context
{
    /**
     * @var State A reference to the current state of the Context.
     */
    private $state;

    public function __construct(State $state)
    {
        $this->transitionTo($state);
    }

    public function getState(): State
    {
        return $this->state;
    }

    /**
     * The Context allows changing the State object at runtime.
     */
    public function transitionTo(State $state): void
    {
        // echo "Context: Transition to " . get_class($state) . ".\n";
        $this->state = $state;
        $this->state->setContext($this);
    }
    /**
     * The Context delegates part of its behavior to the current State object.
     */
    public function open(): void
    {
        $this->state->open();
    }

    public function lost(): void
    {
        $this->state->lost();
    }

    public function borrow(): void
    {
        $this->state->borrow();
    }
    public function overtime(): void
    {
        $this->state->overtime();
    }
    public function buy(): void
    {
        $this->state->buy();
    }
}

/**
 * The base State class declares methods that all Concrete State should
 * implement and also provides a backreference to the Context object, associated
 * with the State. This backreference can be used by States to transition the
 * Context to another State.
 */
abstract class State
{

    protected $context;

    public function getContext(): Context
    {
        return $this->context;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    public function open(): void{
        throw new Exception('NOT implemented for this state');
    }

    public function borrow(): void{
        throw new Exception('NOT implemented for this state');
    }

    public function overtime(): void{
        throw new Exception('NOT implemented for this state');
    }
    public function lost(): void{
        throw new Exception('NOT implemented for this state');
    }

    public function buy(): void{
        throw new Exception('NOT implemented for this state');
    }

    public function isVisible(){
        switch (get_class($this)){
            case 'OpenState':
            case 'LendedState':
            case 'OverTimeState':
                return true;
            case 'LostState':
            case 'SoldState':
                return false;
        }
    }


}

/**
 * Concrete States implement various behaviors, associated with a state of the
 * Context.
 */
class OpenState extends state{
    public function borrow(): void
    {
        $this->context->transitionTo(new LendedState());
    }

    public function buy(): void
    {
        $this->context->transitionTo(new SoldState());
    }
}
class LendedState extends State{

    public function open(): void
    {
        $this->context->transitionTo(new OpenState());
    }

    public function lost(): void
    {
        $this->context->transitionTo(new LostState());
    }
    public function overtime(): void
    {

        $this->context->transitionTo(new OvertimeState());
    }


}
class OvertimeState extends State {
    public function open(): void
    {
        $this->context->transitionTo(new OpenState());
    }

    public function lost(): void
    {
        $this->context->transitionTo(new LostState());
    }
}
class LostState extends State {

}
class SoldState extends State{

}
